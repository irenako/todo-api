<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    public function test_task_belongs_to_user() {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
        $this->assertEquals($user->email, $task->user->email);
        $this->assertEquals($user->login, $task->user->login);
    }
}
