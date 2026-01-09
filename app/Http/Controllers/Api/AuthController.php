<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends BaseApiController
{
    public function register(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
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
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'user_type' => $data['user_type'] ?? 'customer',
            ]);

            $token = $user->generateApiToken();

            Log::info('API User registered', ['user_id' => $user->id]);

            return $this->success([
                'token' => $token,
                'user' => $user,
            ], 'تم إنشاء الحساب بنجاح', 201);
        }, 'حدث خطأ أثناء التسجيل');
    }

    public function login(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            // Find user by email
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['بيانات تسجيل الدخول غير صحيحة'],
                ]);
            }

            // Generate API token
            $token = $user->generateApiToken();

            Log::info('API User logged in', ['user_id' => $user->id]);

            return $this->success([
                'token' => $token,
                'user' => $user->fresh(),
            ], 'تم تسجيل الدخول بنجاح');
        }, 'حدث خطأ أثناء تسجيل الدخول');
    }

    public function logout(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            /** @var User $user */
            $user = $request->user();
            $user?->clearApiToken();

            Log::info('API User logged out', ['user_id' => $user?->id]);

            return $this->success(null, 'تم تسجيل الخروج بنجاح');
        }, 'حدث خطأ أثناء تسجيل الخروج');
    }

    public function profile(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            $user = $request->user()->load([
                'providerProfile',
                'services' => fn($query) => $query->latest()->limit(10),
            ]);

            return $this->success($user);
        }, 'حدث خطأ أثناء تحميل الملف الشخصي');
    }

    public function updateProfile(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            /** @var User $user */
            $user = $request->user();

            $data = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'unique:users,phone,' . $user->id],
                'bio' => ['sometimes', 'nullable', 'string'],
                'user_type' => ['sometimes', 'in:customer,provider'],
            ]);

            $user->update($data);

            Log::info('API User profile updated', ['user_id' => $user->id]);

            return $this->success($user->fresh(), 'تم تحديث الملف الشخصي بنجاح');
        }, 'حدث خطأ أثناء تحديث الملف الشخصي');
    }
}
