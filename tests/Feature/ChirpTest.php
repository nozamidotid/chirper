<?php

namespace Tests\Feature;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChirpTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_authenticate_can_view_chirp_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $chirps = Chirp::with('user')->latest()->get();

        $response = $this->actingAs($user)->get('/chirps', [
            'chirps' => $chirps,
        ]);

        $response->assertStatus(200);
        $response->assertSee($chirps[0]->message);
    }

    public function test_guest_can_not_view_chirp_page(): void
    {

        $response = $this->get('/chirps');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_user_authenticate_can_post_message(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/chirps', [
            'message' => 'Hello World'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/chirps');
    }

    public function test_user_authenticate_can_not_post_without_message(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/chirps');

        $response->assertSessionHasErrors(['message' => 'The message field is required.']);
        $response->assertStatus(302);
        $response->assertRedirect(session()->previousUrl());
    }

    public function test_user_authenticate_can_see_edit_message(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        dd($user);

        $response = $this->actingAs($user)->get('/chirps/'. $user->chirps()->first() .'/edit');

        dd($response);

        $response->assertStatus(200);
        $response->assertSee('Selamat');
    }
}
