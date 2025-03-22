<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\GetTasksRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\TaskStatus;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function index(string $userId, GetTasksRequest $request)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'status');
        $sortOrder = $request->get('sort_order', 'asc');

        return TaskResource::collection(
            $user->tasks()
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage)
        );
    }

    public function store(string $userId, CreateTaskRequest $request)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $task = Task::create([
            'user_id' => $userId,
            'title' => $request->title,
            'description' => $request->description,
            'status' => TaskStatus::NEW->value,
            'start_date' => \Carbon\Carbon::createFromFormat('d-m-Y H:i', $request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => $request->end_date ? \Carbon\Carbon::createFromFormat('d-m-Y H:i', $request->end_date)->format('Y-m-d H:i:s') : null,
        ]);

        if (!$task) {
            return response()->json(['error' => 'Task not created'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Task created successfully'], Response::HTTP_CREATED);
    }

    public function show(string $userId, string $taskId)
    {
        $task = Task::where('id', $taskId)->where('user_id', $userId)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return new TaskResource($task);
    }

    public function update(string $userId, string $taskId, UpdateTaskRequest $request)
    {
        $task = Task::where('id', $taskId)->where('user_id', $userId)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $task->update($request->all());

        return response()->json(['message' => 'Task updated successfully'], Response::HTTP_OK);
    }

    public function destroy(string $userId, string $taskId)
    {
        $task = Task::where('id', $taskId)->where('user_id', $userId)->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], Response::HTTP_OK);
    }

    public function deleteAllNewTasks(string $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->tasks()->where('status', TaskStatus::NEW->value)->delete();

        return response()->json(['message' => 'All new tasks deleted successfully'], Response::HTTP_OK);
    }
}
