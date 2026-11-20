<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymobWebhookTest extends TestCase
{
    private string $hmacSecret = 'sandbox-test-hmac-secret';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.paymob.hmac_secret' => $this->hmacSecret, 'services.paymob.integration_id' => '12345']);
        config(['services.paymob.secret_key' => null, 'services.paymob.public_key' => null]);

        Schema::create('auth_users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->boolean('is_super_admin')->default(false);
            $table->timestamps();
        });
        Schema::create('public_bookings', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('item_type');
            $table->string('item_id');
            $table->string('host_id')->nullable();
            $table->string('status');
            $table->integer('amount');
            $table->timestamps();
        });
        Schema::create('public_user_roles', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('role');
        });
        Schema::create('public_profiles', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('display_name')->nullable();
            $table->string('location')->nullable();
        });
        Schema::create('public_user_contacts', function (Blueprint $table): void {
            $table->string('user_id')->primary();
            $table->string('phone')->nullable();
        });
        Schema::create('payments', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('booking_id')->nullable();
            $table->string('feature_request_id')->nullable();
            $table->string('provider');
            $table->string('status');
            $table->unsignedBigInteger('amount_cents');
            $table->string('currency');
            $table->string('intention_id')->nullable();
            $table->string('paymob_order_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('requires_review')->default(false);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('public_user_roles');
        Schema::dropIfExists('public_user_contacts');
        Schema::dropIfExists('public_profiles');
        Schema::dropIfExists('public_bookings');
        Schema::dropIfExists('auth_users');
        parent::tearDown();
    }

    public function test_valid_success_webhook_marks_payment_paid_and_booking_confirmed_idempotently(): void
    {
        $booking = $this->createBooking();
        $payment = Payment::create(['booking_id' => $booking->id, 'provider' => 'paymob', 'status' => 'pending', 'amount_cents' => 50000, 'currency' => 'EGP', 'paymob_order_id' => '9001']);
        $payload = $this->payload(true);

        $this->postWebhook($payload)->assertOk()->assertJsonPath('received', true);
        $this->postWebhook($payload)->assertOk();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'paid', 'transaction_id' => '6001']);
        $this->assertDatabaseHas('public_bookings', ['id' => $booking->id, 'status' => 'confirmed']);
    }

    public function test_invalid_signature_cannot_change_payment_or_booking(): void
    {
        $booking = $this->createBooking();
        $payment = Payment::create(['booking_id' => $booking->id, 'provider' => 'paymob', 'status' => 'pending', 'amount_cents' => 50000, 'currency' => 'EGP', 'paymob_order_id' => '9001']);

        $this->postJson('/api/v1/payments/paymob/webhook?hmac=bad-signature', $this->payload(true))->assertForbidden();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'pending']);
        $this->assertDatabaseHas('public_bookings', ['id' => $booking->id, 'status' => 'pending']);
    }

    public function test_failed_payment_stays_unconfirmed_and_mismatched_amount_is_rejected(): void
    {
        $booking = $this->createBooking();
        Payment::create(['booking_id' => $booking->id, 'provider' => 'paymob', 'status' => 'pending', 'amount_cents' => 50000, 'currency' => 'EGP', 'paymob_order_id' => '9001']);
        $failure = $this->payload(false);
        $this->postWebhook($failure)->assertOk();
        $this->assertDatabaseHas('payments', ['paymob_order_id' => '9001', 'status' => 'failed']);
        $this->assertDatabaseHas('public_bookings', ['id' => $booking->id, 'status' => 'pending']);

        $mismatch = $this->payload(true);
        $mismatch['obj']['amount_cents'] = 50001;
        $this->postWebhook($mismatch)->assertUnprocessable();
        $this->assertDatabaseHas('public_bookings', ['id' => $booking->id, 'status' => 'pending']);
    }

    public function test_client_cannot_confirm_booking_or_set_its_amount(): void
    {
        $booking = $this->createBooking();
        $user = User::findOrFail($booking->user_id);

        $this->actingAs($user)->patchJson('/api/v1/bookings/'.$booking->id, ['status' => 'confirmed', 'amount' => 1])->assertForbidden();
        $this->actingAs($user)->postJson('/api/v1/bookings/'.$booking->id.'/payment-intentions', ['amount' => 1])->assertServiceUnavailable();
        $this->assertDatabaseHas('public_bookings', ['id' => $booking->id, 'status' => 'pending', 'amount' => 500]);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_status_exposes_latest_attempt_status_for_polling(): void
    {
        $booking = $this->createBooking();
        Payment::create(['booking_id' => $booking->id, 'provider' => 'paymob', 'status' => 'pending', 'amount_cents' => 50000, 'currency' => 'EGP', 'paymob_order_id' => '9001']);
        $user = User::findOrFail($booking->user_id);

        $this->actingAs($user)->getJson('/api/v1/bookings/'.$booking->id.'/payment')
            ->assertOk()->assertJsonPath('data.attempt_status', 'pending')->assertJsonPath('data.payment_status', 'pending');
    }

    public function test_repeated_checkout_start_does_not_create_a_second_live_attempt(): void
    {
        config(['services.paymob.secret_key' => 'sk_test_only', 'services.paymob.public_key' => 'pk_test_only', 'services.paymob.integration_id' => '12345', 'services.paymob.frontend_url' => 'https://ana-refi.test']);
        Http::fake(['https://accept.paymob.com/v1/intention/' => Http::response(['id' => 'pi_test_1', 'intention_order_id' => '9001', 'client_secret' => 'csk_test_1'], 201)]);
        $booking = $this->createBooking();
        \Illuminate\Support\Facades\DB::table('public_user_contacts')->insert(['user_id' => $booking->user_id, 'phone' => '+201111111111']);
        $user = User::findOrFail($booking->user_id);

        $this->actingAs($user)->postJson('/api/v1/bookings/'.$booking->id.'/payment-intentions')->assertCreated()->assertJsonPath('data.status', 'pending');
        $this->actingAs($user)->postJson('/api/v1/bookings/'.$booking->id.'/payment-intentions')->assertStatus(409);
        $this->assertDatabaseCount('payments', 1);
        Http::assertSentCount(1);
    }

    public function test_late_second_charge_is_flagged_and_paid_booking_cannot_be_cancelled(): void
    {
        $booking = $this->createBooking();
        $first = Payment::create(['booking_id' => $booking->id, 'provider' => 'paymob', 'status' => 'paid', 'amount_cents' => 50000, 'currency' => 'EGP', 'paymob_order_id' => '9000', 'transaction_id' => '6000']);
        Payment::create(['booking_id' => $booking->id, 'provider' => 'paymob', 'status' => 'pending', 'amount_cents' => 50000, 'currency' => 'EGP', 'paymob_order_id' => '9001']);
        \Illuminate\Support\Facades\DB::table('public_bookings')->where('id', $booking->id)->update(['status' => 'confirmed']);

        $second = $this->payload(true);
        $second['obj']['order']['id'] = 9001;
        $second['obj']['id'] = 6001;
        $this->postWebhook($second)->assertOk();
        $this->assertDatabaseHas('payments', ['paymob_order_id' => '9001', 'status' => 'paid', 'requires_review' => true]);

        $user = User::findOrFail($booking->user_id);
        $this->actingAs($user)->patchJson('/api/v1/bookings/'.$booking->id, ['status' => 'cancelled'])->assertUnprocessable();
        $this->assertDatabaseHas('payments', ['id' => $first->id, 'status' => 'paid']);
    }

    private function createBooking(): object
    {
        $userId = (string) Str::uuid();
        \Illuminate\Support\Facades\DB::table('auth_users')->insert(['id' => $userId, 'is_super_admin' => false, 'created_at' => now(), 'updated_at' => now()]);
        $id = (string) Str::uuid();
        \Illuminate\Support\Facades\DB::table('public_bookings')->insert(['id' => $id, 'user_id' => $userId, 'item_type' => 'stay', 'item_id' => (string) Str::uuid(), 'host_id' => null, 'status' => 'pending', 'amount' => 500, 'created_at' => now(), 'updated_at' => now()]);
        return (object) ['id' => $id, 'user_id' => $userId];
    }

    private function payload(bool $success): array
    {
        return ['type' => 'TRANSACTION', 'obj' => [
            'amount_cents' => 50000, 'created_at' => '2026-11-20T10:00:00Z', 'currency' => 'EGP',
            'error_occured' => !$success, 'has_parent_transaction' => false, 'id' => 6001,
            'integration_id' => 12345, 'is_3d_secure' => true, 'is_auth' => false, 'is_capture' => false,
            'is_refunded' => false, 'is_standalone_payment' => true, 'is_voided' => false,
            'order' => ['id' => 9001], 'owner' => 2, 'pending' => false,
            'source_data' => ['pan' => '1234', 'sub_type' => 'MasterCard', 'type' => 'card'],
            'success' => $success,
        ]];
    }

    private function postWebhook(array $payload)
    {
        $obj = $payload['obj'];
        $fields = ['amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 'is_voided', 'order.id', 'owner', 'pending', 'source_data.pan', 'source_data.sub_type', 'source_data.type', 'success'];
        $value = static function (array $source, string $path): string {
            $current = $source;
            foreach (explode('.', $path) as $part) $current = $current[$part] ?? null;
            if (is_bool($current)) return $current ? 'true' : 'false';
            return $current === null ? '' : (string) $current;
        };
        $message = implode('', array_map(fn ($field) => $value($obj, $field), $fields));
        $hmac = hash_hmac('sha512', $message, $this->hmacSecret);
        return $this->postJson('/api/v1/payments/paymob/webhook?hmac='.$hmac, $payload);
    }
}
