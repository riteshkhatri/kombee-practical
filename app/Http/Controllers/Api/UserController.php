<?php

namespace App\Http\Controllers\Api;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value');

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $columns = ['id', 'first_name', 'email', 'contact_number', 'state_id'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $query = User::excludeLoggedIn()->with(['documents', 'state', 'city']);

        $recordsTotal = $query->count();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $users = $query->orderBy($orderColumn, $orderDir)->get();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $users,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('user_documents', 'public');
                $user->documents()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles'));
        }

        UserRegistered::dispatch($user);

        return $this->sendResponse($user->load('documents'), 'User created successfully.', 201);
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return $this->sendResponse($user->load(['documents', 'roles']), 'User retrieved successfully.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $data = $request->validated();
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('user_documents', 'public');
                $user->documents()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles'));
        }

        return $this->sendResponse($user->load(['documents', 'roles']), 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();

        return $this->sendResponse(null, 'User deleted successfully.');
    }
}
