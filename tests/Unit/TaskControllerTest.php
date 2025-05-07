<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving a list of tasks.
     */
    public function test_index_tasks()
    {
        $user = User::factory()->create();

        Task::factory()->count(10)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'links',
                'meta',
            ]);
    }

    /**
     * Test creating a new task.
     */
    public function test_store_task()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Test Task',
            'body' => 'This is a test task.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'body',
                    'is_completed',
                ],
            ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    /**
     * Test retrieving a specific task.
     */
    public function test_show_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'body',
                    'is_completed',
                ],
            ]);
    }

    /**
     * Test updating a task.
     */
    public function test_update_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'body' => 'Updated task description.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully.',
            ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Updated Task']);
    }

    /**
     * Test deleting a task.
     */
    public function test_destroy_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully.',
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Test marking a task as completed.
     */
    public function test_complete_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'is_completed' => false]);

        $response = $this->actingAs($user)->patchJson("/api/tasks/{$task->id}/complete", [
            'is_completed' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task marked as completed.',
            ]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => true]);
    }
}
