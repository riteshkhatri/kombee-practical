<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        Gate::authorize('viewAny', Permission::class);
        $permissions = Permission::all();

        return $this->sendResponse(PermissionResource::collection($permissions), 'Permissions retrieved successfully.');
    }

    public function store(StorePermissionRequest $request)
    {
        Gate::authorize('create', Permission::class);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $permission = Permission::create($data);

        return $this->sendResponse(new PermissionResource($permission), 'Permission created successfully.', 201);
    }

    public function show(Permission $permission)
    {
        Gate::authorize('view', $permission);

        return $this->sendResponse(new PermissionResource($permission), 'Permission retrieved successfully.');
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        Gate::authorize('update', $permission);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $permission->update($data);

        return $this->sendResponse(new PermissionResource($permission), 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        Gate::authorize('delete', $permission);
        $permission->delete();

        return $this->sendResponse(null, 'Permission deleted successfully.');
    }
}
