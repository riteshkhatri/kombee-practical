<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Supplier::class);

        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value');

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $columns = ['id', 'name', 'email', 'contact_number', 'address'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $query = Supplier::query();

        $recordsTotal = $query->count();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $suppliers = $query->orderBy($orderColumn, $orderDir)->get();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $suppliers,
        ]);
    }

    public function store(StoreSupplierRequest $request)
    {
        Gate::authorize('create', Supplier::class);

        $supplier = Supplier::create($request->validated());

        return $this->sendResponse($supplier, 'Supplier created successfully.', 201);
    }

    public function show(Supplier $supplier)
    {
        Gate::authorize('view', $supplier);

        return $this->sendResponse($supplier, 'Supplier retrieved successfully.');
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        Gate::authorize('update', $supplier);

        $supplier->update($request->validated());

        return $this->sendResponse($supplier, 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        Gate::authorize('delete', $supplier);

        $supplier->delete();

        return $this->sendResponse(null, 'Supplier deleted successfully.');
    }
}
