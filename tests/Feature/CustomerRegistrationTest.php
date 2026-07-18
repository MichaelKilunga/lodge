<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed required roles
        Role::create(['name' => 'Customer']);
        Role::create(['name' => 'Super']);
        Role::create(['name' => 'Front Desk']);
    }

    public function test_customer_registration_requires_name_and_phone_but_email_is_optional()
    {
        // Login as Super admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Super',
            'role_id' => Role::where('name', 'Super')->first()->id,
            'random_key' => 'key',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('customer.store'), [
                'name' => 'John Doe',
                'phone' => '0712345678',
                // email omitted (optional)
                'address' => 'Test Address',
                'job' => 'Engineer',
                'birthdate' => '1990-01-01',
                'gender' => 'Male',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customer.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'phone' => '0712345678',
            'email' => null,
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'address' => 'Test Address',
        ]);
    }

    public function test_customer_registration_fails_without_phone()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Super',
            'role_id' => Role::where('name', 'Super')->first()->id,
            'random_key' => 'key',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('customer.store'), [
                'name' => 'John Doe',
                // phone omitted!
                'email' => 'john@example.com',
                'address' => 'Test Address',
                'job' => 'Engineer',
                'birthdate' => '1990-01-01',
                'gender' => 'Male',
            ]);

        $response->assertSessionHasErrors(['phone']);
    }

    public function test_updating_customer_updates_associated_user()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Super',
            'role_id' => Role::where('name', 'Super')->first()->id,
            'random_key' => 'key',
        ]);

        // Create a user and customer
        $user = User::create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '0711111111',
            'password' => bcrypt('password'),
            'role' => 'Customer',
            'role_id' => Role::where('name', 'Customer')->first()->id,
            'random_key' => 'key2',
        ]);

        $customer = Customer::create([
            'name' => 'Old Name',
            'address' => 'Old Address',
            'job' => 'Old Job',
            'birthdate' => '1980-01-01',
            'gender' => 'Male',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('customer.update', $customer->id), [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'phone' => '0722222222',
                'address' => 'New Address',
                'job' => 'New Job',
                'birthdate' => '1985-05-05',
                'gender' => 'Female',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customer.index'));

        // Assert user was updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '0722222222',
        ]);

        // Assert customer was updated
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'New Name',
            'address' => 'New Address',
        ]);
    }

    public function test_reservation_processed_by_staff_triggers_notifications()
    {
        // 1. Create a customer with email and phone
        $customerUser = User::create([
            'name' => 'Client Name',
            'email' => 'client@example.com',
            'phone' => '0788888888',
            'password' => bcrypt('password'),
            'role' => 'Customer',
            'role_id' => Role::where('name', 'Customer')->first()->id,
            'random_key' => 'client_key',
        ]);

        $customer = Customer::create([
            'name' => 'Client Name',
            'user_id' => $customerUser->id,
        ]);

        // 2. Create staff
        $staff = User::create([
            'name' => 'Staff Name',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
            'role' => 'Front Desk',
            'role_id' => Role::where('name', 'Front Desk')->first()->id,
            'random_key' => 'staff_key',
        ]);

        // 3. Create room status & room
        $status = RoomStatus::create(['name' => 'Available', 'code' => 'AV', 'information' => 'Ready']);
        $type = Type::create(['name' => 'Deluxe', 'information' => 'Luxury room']);
        $room = Room::create([
            'number' => '101',
            'type_id' => $type->id,
            'room_status_id' => $status->id,
            'capacity' => 2,
            'price' => 100000,
            'view' => 'Garden view',
        ]);

        // 4. Mock Mail to prevent real email sending
        Mail::fake();

        // 5. Fake HTTP client for SMS requests
        Http::fake([
            'pushsms.rehospace.com/*' => Http::response(['status' => 'ok', 'id' => 123], 200),
        ]);

        // 6. Spy on Log to ensure WhatsApp was called
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function($message) {
                return str_contains($message, 'WhatsappService: Message successfully sent to WhatsApp User')
                    && str_contains($message, '255788888888');
            });
        
        Log::shouldReceive('warning');
        Log::shouldReceive('error');
        Log::shouldReceive('debug');

        // Call the endpoint as staff
        $response = $this->actingAs($staff)
            ->post(route('transaction.reservation.payDownPayment', [
                'customer' => $customer->id,
                'room' => $room->id,
            ]), [
                'check_in' => date('Y-m-d'),
                'check_out' => date('Y-m-d', strtotime('+2 days')),
                'downPayment' => 30000,
            ]);

        $response->assertSessionHasNoErrors();

        // Verify email was sent to client
        Mail::assertSent(\App\Mail\BookingConfirmationMail::class, function ($mail) use ($customerUser) {
            return $mail->hasTo($customerUser->email);
        });

        // Assert client SMS log was written (255788888888)
        $this->assertDatabaseHas('sms_logs', [
            'recipient' => '255788888888',
            'status' => 'Success',
        ]);
    }
}
