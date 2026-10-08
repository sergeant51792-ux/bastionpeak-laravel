<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);

        $validator->validate();

        $customerRole = Role::where('name', 'Customer')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'password' => Hash::make($request->password),
            'pin_hash' => Hash::make($request->pin),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        if ($customerRole) {
            $user->assignRole($customerRole);
        }

        $currency = Currency::where('code', 'USD')->first();

        if ($currency) {
            Account::create([
                'user_id' => $user->id,
                'currency_id' => $currency->id,
                'type' => 'main',
                'name' => 'Main Wallet',
                'account_number' => $this->generateAccountNumber(),
                'balance' => 0,
                'status' => 'active',
            ]);
        }

        auth()->login($user);

        return redirect()->route('home');
    }

    private function generateAccountNumber(): string
    {
        $parts = [
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
        ];

        return '10 ' . implode(' ', $parts);
    }
}
