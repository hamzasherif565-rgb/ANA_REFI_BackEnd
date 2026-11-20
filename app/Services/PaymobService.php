<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymobService
{
    public function createCheckout(Payment $payment, User $user, string $description, string $returnUrl): array
    {
        $secretKey = config('services.paymob.secret_key');
        $publicKey = config('services.paymob.public_key');
        $integrationId = config('services.paymob.integration_id');
        $frontendUrl = rtrim((string) config('services.paymob.frontend_url'), '/');
        if (!$secretKey || !$publicKey || !$integrationId || !$frontendUrl) {
            throw new RuntimeException('Paymob is not configured. Add test credentials before enabling checkout.');
        }
        if (!str_starts_with($frontendUrl, 'https://')) {
            throw new RuntimeException('PAYMOB_FRONTEND_URL must use HTTPS.');
        }

        $user->loadMissing(['profileById', 'userContactByUserId']);
        $profile = $user->profileById;
        $name = preg_split('/\s+/', trim((string) ($profile?->display_name ?: 'ANA Refi Customer')), 2);
        $response = Http::acceptJson()->asJson()->withToken($secretKey, 'Token')
            ->timeout(20)->post('https://accept.paymob.com/v1/intention/', [
                'amount' => $payment->amount_cents,
                'currency' => $payment->currency,
                'payment_methods' => [(int) $integrationId],
                'items' => [[
                    'name' => $description,
                    'amount' => $payment->amount_cents,
                    'description' => 'Booking payment',
                    'quantity' => 1,
                ]],
                'billing_data' => [
                    'apartment' => 'NA', 'first_name' => $name[0] ?: 'Customer',
                    'last_name' => $name[1] ?? 'ANA Refi', 'street' => 'NA', 'building' => 'NA',
                    'phone_number' => (string) ($user->phone ?: $user->userContactByUserId?->first()?->phone),
                    'city' => $profile?->location ?: 'Cairo', 'country' => 'EG', 'email' => $user->email,
                    'floor' => 'NA', 'state' => $profile?->location ?: 'Cairo',
                ],
                'special_reference' => $payment->id,
                'expiration' => 1800,
                'notification_url' => route('api.v1.payments.paymob.webhook'),
                'redirection_url' => $returnUrl,
                'extras' => ['payment_id' => $payment->id, 'booking_id' => $payment->booking_id, 'feature_request_id' => $payment->feature_request_id],
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Paymob checkout could not be created (HTTP '.$response->status().').');
        }
        $intention = $response->json();
        if (!is_array($intention) || empty($intention['client_secret']) || empty($intention['id'])) {
            throw new RuntimeException('Paymob returned an incomplete checkout response.');
        }

        return [
            'intention_id' => (string) $intention['id'],
            'order_id' => isset($intention['intention_order_id']) ? (string) $intention['intention_order_id'] : null,
            'checkout_url' => 'https://eg.checkout.paymob.com/?publicKey='.urlencode($publicKey).'&clientSecret='.urlencode($intention['client_secret']),
        ];
    }

    public function verifyWebhook(array $payload, ?string $providedHmac): bool
    {
        $secret = config('services.paymob.hmac_secret');
        $obj = $payload['obj'] ?? null;
        if (!$secret || !$providedHmac || !is_array($obj)) return false;

        $fields = [
            'amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction',
            'id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded',
            'is_standalone_payment', 'is_voided', 'order.id', 'owner', 'pending',
            'source_data.pan', 'source_data.sub_type', 'source_data.type', 'success',
        ];
        $value = static function (array $source, string $path): string {
            $current = $source;
            foreach (explode('.', $path) as $part) {
                if (!is_array($current) || !array_key_exists($part, $current)) return '';
                $current = $current[$part];
            }
            if (is_bool($current)) return $current ? 'true' : 'false';
            if ($current === null) return '';
            return is_scalar($current) ? (string) $current : '';
        };
        $message = implode('', array_map(fn (string $field): string => $value($obj, $field), $fields));
        return hash_equals(hash_hmac('sha512', $message, $secret), strtolower($providedHmac));
    }
}
