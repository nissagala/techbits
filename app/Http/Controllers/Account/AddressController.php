<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Rules\SriLankanPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function addressRules(): array
    {
        return [
            'label'       => 'nullable|string|max:30',
            'recipient'   => 'required|string|min:2|max:100',
            'line1'       => 'required|string|min:3|max:200',
            'line2'       => 'nullable|string|max:200',
            'city'        => 'required|string|min:2|max:50',
            'district'    => 'required|string|in:' . implode(',', config('districts')),
            'postal_code' => 'required|digits:5',
            'phone'       => ['required', new SriLankanPhone],
            'is_default'  => 'boolean',
        ];
    }

    public function index()
    {
        $addresses = Auth::user()->addresses()->get();
        return view('account.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('account.addresses.form');
    }

    public function store(Request $request)
    {
        $request->validate($this->addressRules());

        $user = Auth::user();

        if ($user->addresses()->count() >= 10) {
            return back()->withErrors(['limit' => 'You can have a maximum of 10 saved addresses.']);
        }

        $isFirst   = $user->addresses()->count() === 0;
        $isDefault = $isFirst || $request->boolean('is_default');

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $phone = SriLankanPhone::normalize($request->phone);

        $user->addresses()->create(array_merge(
            $request->only(['label', 'recipient', 'line1', 'line2', 'city', 'district', 'postal_code']),
            ['phone' => $phone, 'is_default' => $isDefault]
        ));

        return redirect()->route('account.addresses.index')->with('success', 'Address saved.');
    }

    public function edit(Address $address)
    {
        $this->authorizeAddress($address);
        return view('account.addresses.form', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);
        $request->validate($this->addressRules());

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $phone = SriLankanPhone::normalize($request->phone);

        $address->update(array_merge(
            $request->only(['label', 'recipient', 'line1', 'line2', 'city', 'district', 'postal_code']),
            ['phone' => $phone, 'is_default' => $request->boolean('is_default')]
        ));

        return redirect()->route('account.addresses.index')->with('success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            Auth::user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return redirect()->route('account.addresses.index')->with('success', 'Address deleted.');
    }

    public function setDefault(Address $address)
    {
        $this->authorizeAddress($address);
        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return redirect()->route('account.addresses.index')->with('success', 'Default address updated.');
    }

    private function authorizeAddress(Address $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
