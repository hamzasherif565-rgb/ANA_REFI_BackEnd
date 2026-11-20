<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function createIntent(Request $request, Booking $booking, PaymobService $paymob): JsonResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === 'pending', 422, 'Only pending bookings can be paid.');
        abort_if($booking->amount <= 0, 422, 'This booking does not require an electronic payment.');
        abort_if(!config('services.paymob.secret_key') || !config('services.paymob.public_key') || !config('services.paymob.integration_id') || !config('services.paymob.hmac_secret'), 503, 'Electronic payments are not configured yet.');
        $phone = $request->user()->phone ?: $request->user()->userContactByUserId()->value('phone');
        abort_unless($phone, 422, 'Add a phone number to your profile before paying.');

        $payment = DB::transaction(function () use ($booking): Payment {
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'pending', 422, 'Only pending bookings can be paid.');
            abort_if(Payment::where('booking_id', $locked->id)->where('status', 'paid')->exists(), 409, 'This booking has already been paid.');
            abort_if(Payment::where('booking_id', $locked->id)->where('status', 'pending')->where('expires_at', '>', now())->exists(), 409, 'A payment attempt is already pending.');
            return Payment::create([
                'booking_id' => $locked->id, 'provider' => 'paymob', 'status' => 'pending',
                'amount_cents' => $locked->amount * 100, 'currency' => 'EGP',
                'expires_at' => now()->addMinutes(30),
            ]);
        });

        try {
            $intent = $paymob->createCheckout($payment, $request->user(), 'ANA Refi booking '.$booking->id, rtrim(config('services.paymob.frontend_url'), '/').'/payment/'.$booking->id);
            $payment->update(['intention_id' => $intent['intention_id'], 'paymob_order_id' => $intent['order_id']]);
        } catch (Throwable $error) {
            $payment->update(['status' => 'failed']);
            Log::error('Paymob intention creation failed', ['payment_id' => $payment->id, 'reason' => $error->getMessage()]);
            return response()->json(['message' => $error->getMessage()], str_contains($error->getMessage(), 'not configured') || str_contains($error->getMessage(), 'PAYMOB_FRONTEND_URL') ? 503 : 502);
        }

        return response()->json(['data' => [
            'payment_id' => $payment->id, 'status' => $payment->status,
            'amount_cents' => $payment->amount_cents, 'currency' => $payment->currency,
            'checkout_url' => $intent['checkout_url'],
        ]], 201);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        $isAdmin = $request->user()->is_super_admin || $request->user()->userRoleByUserId()->where('role', 'admin')->exists();
        abort_unless($booking->user_id === $request->user()->id || $booking->host_id === $request->user()->id || $isAdmin, 403);
        $payment = Payment::where('booking_id', $booking->id)->latest()->first();
        return response()->json(['data' => [
            'booking_status' => $booking->status,
            'payment_status' => $payment?->status ?? 'unpaid',
            'amount_cents' => $payment?->amount_cents ?? $booking->amount * 100,
            'currency' => $payment?->currency ?? 'EGP',
            'attempt_expires_at' => $payment?->expires_at,
            'attempt_status' => $payment?->status ?? 'unpaid',
            'requires_review' => $payment?->requires_review ?? false,
        ]]);
    }

    public function paymobWebhook(Request $request, PaymobService $paymob): JsonResponse
    {
        $payload = $request->all();
        abort_unless($paymob->verifyWebhook($payload, $request->query('hmac')), 403, 'Invalid Paymob webhook signature.');
        $obj = $payload['obj'];
        abort_unless((string) ($obj['integration_id'] ?? '') === (string) config('services.paymob.integration_id'), 422, 'Unexpected Paymob integration.');

        $payment = DB::transaction(function () use ($obj): ?Payment {
            $orderId = data_get($obj, 'order.id');
            $query = Payment::where('paymob_order_id', (string) $orderId)->lockForUpdate();
            $payment = $query->first();
            if (!$payment) return null;
            if ((int) ($obj['amount_cents'] ?? 0) !== $payment->amount_cents || ($obj['currency'] ?? null) !== $payment->currency) {
                return null;
            }
            if (filter_var($obj['pending'] ?? false, FILTER_VALIDATE_BOOLEAN)) return $payment;

            $successful = filter_var($obj['success'] ?? false, FILTER_VALIDATE_BOOLEAN)
                && !filter_var($obj['error_occured'] ?? false, FILTER_VALIDATE_BOOLEAN)
                && !filter_var($obj['is_refunded'] ?? false, FILTER_VALIDATE_BOOLEAN)
                && !filter_var($obj['is_voided'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($payment->status === 'paid') return $payment;
            $targetPayments = Payment::where('status', 'paid')->where('id', '!=', $payment->id);
            $targetPayments = $payment->booking_id
                ? $targetPayments->where('booking_id', $payment->booking_id)
                : $targetPayments->where('feature_request_id', $payment->feature_request_id);
            $requiresReview = $targetPayments->exists();
            if ($payment->booking_id) {
                $requiresReview = $requiresReview || $payment->booking()->value('status') !== 'pending';
            } elseif ($payment->feature_request_id) {
                $feature = $payment->featureRequest()->first();
                $requiresReview = $requiresReview || !$feature || $feature->status !== 'pending' || $feature->payment_status === 'paid';
            }
            $payment->update([
                'status' => $successful ? 'paid' : 'failed',
                'transaction_id' => isset($obj['id']) ? (string) $obj['id'] : null,
                'requires_review' => $successful && $requiresReview,
            ]);
            if ($successful && !$requiresReview && $payment->booking()->where('status', 'pending')->exists()) {
                $payment->booking()->update(['status' => 'confirmed']);
            }
            if ($successful && $payment->feature_request_id) {
                $payment->featureRequest()->update(['payment_status' => 'paid']);
            }
            return $payment->refresh();
        });

        if (!$payment) return response()->json(['message' => 'Payment reference, amount, or currency did not match.'], 422);
        if ($payment->requires_review) {
            Log::critical('Paymob charge requires manual review', ['payment_id' => $payment->id, 'booking_id' => $payment->booking_id, 'feature_request_id' => $payment->feature_request_id]);
        }
        return response()->json(['received' => true]);
    }
}
