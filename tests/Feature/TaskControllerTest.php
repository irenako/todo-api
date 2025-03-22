<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use App\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_tasks()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks");

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_can_list_tasks_with_pagination()
    {
        Task::factory()->count(15)->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks");

        $response->assertStatus(200);
        $this->assertEquals(15, json_decode($response->getContent())->meta->total);
    }

    public function test_can_list_tasks_with_sorting_by_title_asc()
    {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'A Task']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'B Task']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'C Task']);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks?sort_by=title&sort_order=asc");

        $response->assertStatus(200);
        $this->assertEquals('A Task', $response->json('data')[0]['title']);
        $this->assertEquals('B Task', $response->json('data')[1]['title']);
        $this->assertEquals('C Task', $response->json('data')[2]['title']);
    }

    public function test_can_list_tasks_with_sorting_by_title_desc()
    {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'A Task']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'B Task']);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'C Task']);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks?sort_by=title&sort_order=desc");

        $response->assertStatus(200);
        $this->assertEquals('C Task', $response->json('data')[0]['title']);
        $this->assertEquals('B Task', $response->json('data')[1]['title']);
        $this->assertEquals('A Task', $response->json('data')[2]['title']);
    }

    public function test_can_list_tasks_with_sorting_by_status_asc()
    {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'A Task', 'status' => TaskStatus::NEW->value]);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'B Task', 'status' => TaskStatus::IN_PROGRESS->value]);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'C Task', 'status' => TaskStatus::FINISHED->value]);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks?sort_by=status&sort_order=asc");

        $response->assertStatus(200);
        $this->assertEquals('A Task', $response->json('data')[0]['title']);
        $this->assertEquals('B Task', $response->json('data')[1]['title']);
        $this->assertEquals('C Task', $response->json('data')[2]['title']);
    }

    public function test_can_list_tasks_with_sorting_by_status_desc()
    {
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'A Task', 'status' => TaskStatus::NEW->value]);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'B Task', 'status' => TaskStatus::IN_PROGRESS->value]);
        Task::factory()->create(['user_id' => $this->user->id, 'title' => 'C Task', 'status' => TaskStatus::FINISHED->value]);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks?sort_by=status&sort_order=desc");

        $response->assertStatus(200);
        $this->assertEquals('C Task', $response->json('data')[0]['title']);
        $this->assertEquals('B Task', $response->json('data')[1]['title']);
        $this->assertEquals('A Task', $response->json('data')[2]['title']);
    }

    public function test_cannot_list_tasks_with_invalid_user_id()
    {
        $invalidUserId = $this->user->id + 1;
        $response = $this->getJson("/api/users/{$invalidUserId}/tasks");

        $response->assertStatus(404);
    }

    public function test_cannot_list_tasks_with_invalid_sort_by()
    {
        $response = $this->getJson("/api/users/{$this->user->id}/tasks?sort_by=invalid&sort_order=asc");

        $response->assertStatus(422);
    }

    public function test_cannot_list_tasks_with_invalid_sort_order()
    {
        $response = $this->getJson("/api/users/{$this->user->id}/tasks?sort_by=title&sort_order=invalid");

        $response->assertStatus(422);
    }

    public function test_can_create_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'start_date' => '21-03-2024 10:00',
            'end_date' => '22-03-2024 10:00'
        ];

        $response = $this->postJson("/api/users/{$this->user->id}/tasks", $taskData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Task created successfully']);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Test Task',
            'status' => TaskStatus::NEW->value
        ]);
    }

    public function test_cannot_create_task_with_empty_title()
    {
        $taskData = [
            'title' => '',
            'description' => 'Test Description',
        ];
        $response = $this->postJson("/api/users/{$this->user->id}/tasks", $taskData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_task_with_empty_description()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => '',
        ];
        $response = $this->postJson("/api/users/{$this->user->id}/tasks", $taskData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_task_with_invalid_start_date()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'start_date' => 'invalid',
            'end_date' => '22-03-2024 10:00'
        ];
        $response = $this->postJson("/api/users/{$this->user->id}/tasks", $taskData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_task_with_invalid_end_date()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'start_date' => '21-03-2024 10:00',
            'end_date' => 'invalid'
        ];
        $response = $this->postJson("/api/users/{$this->user->id}/tasks", $taskData);

        $response->assertStatus(422);
    }

    public function test_cannot_show_task_with_invalid_user_id()
    {
        $invalidUserId = $this->user->id + 1;
        $response = $this->getJson("/api/users/{$invalidUserId}/tasks/1");

        $response->assertStatus(404);
    }

    public function test_can_show_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/users/{$this->user->id}/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertEquals($task->title, $response->json('data')['title']);
        $this->assertEquals($task->description, $response->json('data')['description']);
        $this->assertEquals($task->status, $response->json('data')['status']);
    }

    public function test_cannot_show_task_with_invalid_task_id()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $invalidTaskId = $task->id + 1;
        $response = $this->getJson("/api/users/{$this->user->id}/tasks/{$invalidTaskId}");

        $response->assertStatus(404);
    }

    public function test_can_update_task_title()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'title' => 'Updated Task',
            'status' => TaskStatus::IN_PROGRESS->value
        ];

        $response = $this->putJson("/api/users/{$this->user->id}/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task updated successfully']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => TaskStatus::IN_PROGRESS->value
        ]);
    }

    public function test_can_update_task_status()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'status' => TaskStatus::FINISHED->value
        ];

        $response = $this->putJson("/api/users/{$this->user->id}/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task updated successfully']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => TaskStatus::FINISHED->value
        ]);
    }

    public function test_can_update_task_description()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'description' => 'Updated Description'
        ];

        $response = $this->putJson("/api/users/{$this->user->id}/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task updated successfully']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'description' => 'Updated Description'
        ]);
    }

    public function test_cannot_update_task_with_invalid_task_id()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'description' => 'Updated Description'
        ];
        $invalidTaskId = $task->id + 1;
        $response = $this->putJson("/api/users/{$this->user->id}/tasks/{$invalidTaskId}", $updateData);

        $response->assertStatus(404);
    }

    public function test_cannot_update_task_with_invalid_status()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'status' => 'invalid'
        ];

        $response = $this->putJson("/api/users/{$this->user->id}/tasks/{$task->id}", $updateData);

        $response->assertStatus(422);
    }


    public function test_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/users/{$this->user->id}/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }


    public function test_cannot_delete_task_with_invalid_task_id()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $invalidTaskId = $task->id + 1;
        $response = $this->deleteJson("/api/users/{$this->user->id}/tasks/{$invalidTaskId}");

        $response->assertStatus(404);
    }

    public function test_cannot_delete_task_with_invalid_user_id()
    {
        $invalidUserId = $this->user->id + 1;
        $response = $this->deleteJson("/api/users/{$invalidUserId}/tasks/1");

        $response->assertStatus(404);
    }

    public function test_can_delete_all_new_tasks()
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::NEW->value
        ]);
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::IN_PROGRESS->value
        ]);

        $response = $this->deleteJson("/api/users/{$this->user->id}/tasks");

        $response->assertStatus(200)
            ->assertJson(['message' => 'All new tasks deleted successfully']);

        $this->assertEquals(0, Task::where('user_id', $this->user->id)
            ->where('status', TaskStatus::NEW->value)
            ->count());
        $this->assertEquals(2, Task::where('user_id', $this->user->id)
            ->where('status', TaskStatus::IN_PROGRESS->value)
            ->count());
    }
}
