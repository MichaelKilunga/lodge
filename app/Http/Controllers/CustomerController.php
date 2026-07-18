<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\ImageRepositoryInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function index(Request $request)
    {
        $customers = $this->customerRepository->get($request);

        return view('customer.index', ['customers' => $customers]);
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerRepository->store($request);

        return redirect('customer')->with('success', 'Customer '.$customer->name.' created');
    }

    public function show(Customer $customer)
    {
        return view('customer.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', ['customer' => $customer]);
    }

    public function update(Customer $customer, StoreCustomerRequest $request)
    {
        $customer->update($request->all());
        if ($customer->user) {
            $customer->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
        }

        return redirect('customer')->with('success', 'customer '.$customer->name.' udpated!');
    }

    public function destroy(Customer $customer, ImageRepositoryInterface $imageRepository)
    {
        try {
            if ($customer->transactions()->count() > 0) {
                return redirect('customer')->with('failed', 'Customer '.$customer->name.' cannot be deleted because they have existing booking transactions.');
            }

            $user = $customer->user ? User::find($customer->user->id) : null;
            $avatar_path = $user ? public_path('img/user/'.$user->name.'-'.$user->id) : null;

            $customer->delete();
            if ($user) {
                $user->delete();
            }

            if ($avatar_path && is_dir($avatar_path)) {
                $imageRepository->destroy($avatar_path);
            }

            return redirect('customer')->with('success', 'Customer '.$customer->name.' deleted successfully!');
        } catch (\Exception $e) {
            $errorMessage = '';
            if (isset($e->errorInfo[0]) && $e->errorInfo[0] == '23000') {
                $errorMessage = 'Data still connected to other tables.';
            } else {
                $errorMessage = $e->getMessage();
            }

            return redirect('customer')->with('failed', 'Customer '.$customer->name.' cannot be deleted! '.$errorMessage);
        }
    }
}
