<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\TaskStatus;
use Illuminate\Support\Facades\DB;

class TaskStatController extends Controller
{
    public function getUserTasksStats(string $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $totals = DB::table('tasks')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as new', [TaskStatus::NEW->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as in_progress', [TaskStatus::IN_PROGRESS->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as finished', [TaskStatus::FINISHED->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as failed', [TaskStatus::FAILED->value])
            ->where('user_id', $userId)
            ->first();

        return response()->json(['data' => $totals], Response::HTTP_OK);
    }

    public function getAppTasksStats()
    {
        $totals = DB::table('tasks')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as new', [TaskStatus::NEW->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as in_progress', [TaskStatus::IN_PROGRESS->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as finished', [TaskStatus::FINISHED->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as failed', [TaskStatus::FAILED->value])
            ->first();

        return response()->json(['data' => $totals], Response::HTTP_OK);
    }
}
