<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function test_user_has_many_tasks() {
        $user = User::factory()->create();
        Task::factory(3)->create([
            'user_id' => $user->id
        ]);

        $this->assertEquals(3, $user->tasks->count());
    }

    public function test_on_user_delete_all_tasks_are_deleted() {
        $user = User::factory()->create();
        Task::factory(3)->create([
            'user_id' => $user->id
        ]);

        $user->delete();

        $this->assertEquals(0, Task::where('user_id', $user->id)->count());
    }
}
