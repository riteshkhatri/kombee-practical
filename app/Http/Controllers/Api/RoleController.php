<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    use ApiResponse;

    public function index()
    {
        Gate::authorize('viewAny', Role::class);
        $roles = Role::with('permissions')->get();

        return $this->sendResponse(RoleResource::collection($roles), 'Roles retrieved successfully.');
    }

    public function store(StoreRoleRequest $request)
    {
        Gate::authorize('create', Role::class);
        $role = Role::create($request->validated());

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return $this->sendResponse(new RoleResource($role->load('permissions')), 'Role created successfully.', 201);
    }

    public function show(Role $role)
    {
        Gate::authorize('view', $role);

        return $this->sendResponse(new RoleResource($role->load('permissions')), 'Role retrieved successfully.');
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        Gate::authorize('update', $role);
        $role->update($request->validated());

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return $this->sendResponse(new RoleResource($role->load('permissions')), 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('delete', $role);
        $role->delete();

        return $this->sendResponse(null, 'Role deleted successfully.');
    }
}
