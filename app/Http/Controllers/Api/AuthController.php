<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'user_type' => ['nullable', 'in:customer,provider'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'user_type' => $data['user_type'] ?? 'customer',
        ]);

        $token = $user->generateApiToken();

        return $this->success([
            'token' => $token,
            'user' => $user,
        ], 'تم إنشاء الحساب بنجاح', 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات تسجيل الدخول غير صحيحة'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->generateApiToken();

        return $this->success([
            'token' => $token,
            'user' => $user->fresh(),
        ], 'تم تسجيل الدخول بنجاح');
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user?->clearApiToken();

        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    public function profile(Request $request)
    {
        return $this->success($request->user()->load([
            'providerProfile',
            'services' => fn ($query) => $query->latest()->limit(10),
        ]));
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'bio' => ['sometimes', 'nullable', 'string'],
            'user_type' => ['sometimes', 'in:customer,provider'],
        ]);

        $user->update($data);

        return $this->success($user->fresh(), 'تم تحديث الملف الشخصي بنجاح');
    }
}



