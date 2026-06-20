<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_page_is_renderable(): void
    {
        $this->get('/auth')->assertStatus(200);
        $this->get('/auth?view=login')->assertStatus(200);
        $this->get('/auth?view=register')->assertStatus(200);
        $this->get('/auth?view=forgot')->assertStatus(200);
    }

    public function test_auth_page_includes_login_form_action(): void
    {
        $html = $this->get('/auth')->getContent();
        $this->assertStringContainsString('action="' . route('login') . '"', $html);
        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('name="password"', $html);
    }

    public function test_auth_page_includes_register_form_action(): void
    {
        $html = $this->get('/auth?view=register')->getContent();
        $this->assertStringContainsString('action="' . route('register') . '"', $html);
        $this->assertStringContainsString('name="name"', $html);
        $this->assertStringContainsString('name="password_confirmation"', $html);
    }

    public function test_auth_page_includes_forgot_password_form(): void
    {
        $html = $this->get('/auth?view=forgot')->getContent();
        $this->assertStringContainsString('action="' . route('password.email') . '"', $html);
    }

    public function test_auth_page_includes_csrf_meta(): void
    {
        $html = $this->get('/auth')->getContent();
        $this->assertStringContainsString('csrf-token', $html);
    }

    public function test_login_json_returns_validation_errors(): void
    {
        $response = $this->postJson('/login', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_json_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }

    public function test_login_json_fails_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
        $this->assertGuest();
    }

    public function test_register_json_creates_user(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJson(['ok' => true]);
    }

    public function test_register_json_sends_verification_notification(): void
    {
        \Illuminate\Support\Facades\Notification::fake();

        $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'verify@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(201);

        $user = \App\Models\User::where('email', 'verify@example.com')->firstOrFail();
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \Illuminate\Auth\Notifications\VerifyEmail::class
        );
    }

    public function test_register_json_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_register_json_validates_password_confirmation(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_forgot_password_json_sends_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->postJson('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
    }

    public function test_forgot_password_json_validates_email(): void
    {
        $response = $this->postJson('/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_register_native_form_submission_works(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        \Illuminate\Support\Facades\Event::fake([\Illuminate\Auth\Events\Registered::class]);

        $response = $this->post('/register', [
            'name' => 'Native User',
            'email' => 'native@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseHas('users', ['email' => 'native@example.com']);
        $response->assertRedirect(route('auth.page', ['view' => 'verify', 'email' => 'native@example.com']));
    }

    public function test_register_native_form_validation_error_redirects_back(): void
    {
        $response = $this->from('/auth?view=register')->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertRedirect('/auth?view=register');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_resend_verification_as_guest_with_email(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        $user = User::factory()->unverified()->create();

        $response = $this->postJson('/email/verification-notification', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \Illuminate\Auth\Notifications\VerifyEmail::class
        );
    }

    public function test_resend_verification_succeeds_even_for_unknown_email(): void
    {
        $response = $this->postJson('/email/verification-notification', [
            'email' => 'nobody@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
    }
}
