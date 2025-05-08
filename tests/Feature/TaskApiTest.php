<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_authenticated_user_can_create_task()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/todo/insert/update', [
            'title' => 'Test Task',
            'body' => 'This is a test task.',
        ]);

        $response->assertStatus(201)
        ->assertJsonPath('task.title', 'Test Task');

    }

    public function test_guest_cannot_create_task()
    {
        $response = $this->postJson('/api/todo/insert/update', [
            'title' => 'Unauthorized Task',
            'body' => 'Should not be allowed',
        ]);

        $response->assertStatus(401); // unauthorized
    }
}
