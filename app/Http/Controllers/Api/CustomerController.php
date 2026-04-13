<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Customer::class);

        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value');

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $columns = ['id', 'name', 'email', 'contact_number', 'address'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $query = Customer::query();

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

        $customers = $query->orderBy($orderColumn, $orderDir)->get();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $customers,
        ]);
    }

    public function store(StoreCustomerRequest $request)
    {
        Gate::authorize('create', Customer::class);

        $customer = Customer::create($request->validated());

        return $this->sendResponse($customer, 'Customer created successfully.', 201);
    }

    public function show(Customer $customer)
    {
        Gate::authorize('view', $customer);

        return $this->sendResponse($customer, 'Customer retrieved successfully.');
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        Gate::authorize('update', $customer);

        $customer->update($request->validated());

        return $this->sendResponse($customer, 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        Gate::authorize('delete', $customer);

        $customer->delete();

        return $this->sendResponse(null, 'Customer deleted successfully.');
    }
}
