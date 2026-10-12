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
use Illuminate\Support\Str;
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
            'status' => 'pending',
            'email_verified_at' => now(),
        ]);

        if ($customerRole) {
            $user->assignRole($customerRole);
        }

        return redirect()->route('register')->with('registered', true);
    }

    public static function provisionAccount(User $user): Account
    {
        $currency = Currency::where('code', 'USD')->first();

        if (! $currency) {
            $currency = Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'decimals' => 2,
                'exchange_rate' => 1,
            ]);
        }

        $existing = Account::where('user_id', $user->id)->first();

        if ($existing) {
            return $existing;
        }

        return Account::create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
            'type' => 'main',
            'name' => 'Primary Account',
            'account_number' => self::generateAccountNumber(),
            'balance' => 0,
            'status' => 'active',
        ]);
    }

    public static function generateAccountNumber(): string
    {
        $parts = [
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
        ];

        return '10 ' . implode(' ', $parts);
    }
}
