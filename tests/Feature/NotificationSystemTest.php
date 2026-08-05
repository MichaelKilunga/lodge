<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NewRoomReservationDownPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_user_can_access_notification_center()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('notification.index'));

        $response->assertStatus(200);
        $response->assertSee('Notification Center');
    }

    public function test_route_to_notification_marks_as_read_and_redirects()
    {
        $user = User::factory()->create();
        
        // Create mock database notification
        $notification = $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TestNotification',
            'data' => [
                'message' => 'Test message',
                'url' => route('dashboard.index'),
            ],
            'read_at' => null,
        ]);

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)->get(route('notification.routeTo', ['id' => $notification->id]));

        $response->assertRedirect(route('dashboard.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_route_to_invalid_notification_redirects_safely_without_error()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('notification.routeTo', ['id' => 'non-existent-uuid']));

        $response->assertRedirect(route('notification.index'));
        $response->assertSessionHas('failed');
    }

    public function test_mark_all_as_read()
    {
        $user = User::factory()->create();

        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test 1', 'url' => '#'],
            'read_at' => null,
        ]);

        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test 2', 'url' => '#'],
            'read_at' => null,
        ]);

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->get(route('notification.markAllAsRead'));

        $response->assertSessionHas('success');
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_mark_single_as_read()
    {
        $user = User::factory()->create();

        $notification = $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test 1', 'url' => '#'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('notification.markAsRead', ['id' => $notification->id]));

        $response->assertSessionHas('success');
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
