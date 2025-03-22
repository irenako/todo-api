<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskStatControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_task_stats(): void
    {
        Task::factory()->count(4)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::NEW->value
        ]);
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::IN_PROGRESS->value
        ]);
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::FINISHED->value
        ]);
        Task::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::FAILED->value
        ]);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks/stats");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'total' => 10,
                'new' => 4,
                'in_progress' => 3,
                'finished' => 2,
                'failed' => 1
            ]
        ]);
    }

    public function test_can_get_app_task_stats(): void
    {
        Task::factory()->count(4)->create([
            'status' => TaskStatus::NEW->value
        ]);
        Task::factory()->count(3)->create([
            'status' => TaskStatus::IN_PROGRESS->value
        ]);
        Task::factory()->count(2)->create([
            'status' => TaskStatus::FINISHED->value
        ]);
        Task::factory()->count(1)->create([
            'status' => TaskStatus::FAILED->value
        ]);

        $response = $this->getJson('/api/tasks/stats');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'total' => 10,
                'new' => 4,
                'in_progress' => 3, 
                'finished' => 2,
                'failed' => 1
            ]
        ]);
    }
}
