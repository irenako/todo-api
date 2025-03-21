<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function store(CreateUserRequest $request)
    {
        $user = User::create($request->all());

        if (!$user) {
            return response()->json(['error' => 'User not created'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'User created successfully'], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->all());

        return response()->json(['message' => 'User updated successfully'], Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], Response::HTTP_OK);
    }
}
