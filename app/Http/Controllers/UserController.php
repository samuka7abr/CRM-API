<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('users.manage');
        $q = $request->string('q');
        $role = $request->string('role');
        $status = $request->string('status');

        $users = User::query()
            ->when($q, fn($qq) => $qq->where(function($x) use ($q) {
                $x->where('name','like',"%$q%")->orWhere('email','like',"%$q%");
            }))
            ->when($role, fn($qq) => $qq->where('role', $role))
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->paginate(15);

        return response()->json($users);
    }

    public function show(User $user)
    {
        $this->authorize('users.manage');
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $this->authorize('users.manage');
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'role' => ['required', Rule::in(['admin','manager','agent'])],
            'status' => ['required', Rule::in(['active','inactive'])],
            'phone' => ['nullable','string','max:30'],
            'manager_id' => ['nullable','exists:users,id'],
        ]);

        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $auth = $request->user();
        $isAdmin = $auth && $auth->role === 'admin';

        if (!$auth || ($auth->id !== $user->id && !$isAdmin)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $rules = [
            'name' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['sometimes','string','min:6'],
            'phone' => ['sometimes','nullable','string','max:30'],
        ];

        if ($isAdmin) {
            $rules['role'] = ['sometimes', Rule::in(['admin','manager','agent'])];
            $rules['status'] = ['sometimes', Rule::in(['active','inactive'])];
            $rules['manager_id'] = ['sometimes','nullable','exists:users,id'];
        }

        $data = $request->validate($rules);
        if (!$isAdmin) {
            unset($data['role'], $data['status'], $data['manager_id']);
        }

        $user->update($data);
        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('users.delete');
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
