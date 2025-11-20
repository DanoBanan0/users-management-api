<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // this function will return all users
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    // this function will create a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    // this function will return a specific user by id
    public function show(User $userId)
    {
        return response()->json($userId);
    }

    /**
     * Update the specified resource in storage.
     */
    // this function will update a specific user by id
    public function update(Request $request, User $userId)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|required|string|email|max:100|unique:users,email,' . $userId->id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if ($request->has('name')) {
            $userId->name = $request->name;
        }
        if ($request->has('email')) {
            $userId->email = $request->email;
        }
        if ($request->has('password')) {
            $userId->password = Hash::make($request->password);
        }
        $userId->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $userId
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    // this function will delete a specific user by id
    public function destroy(User $userId)
    {
        $userId->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
