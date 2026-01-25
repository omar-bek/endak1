<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\ProviderProfile;
use App\Models\ProviderCategory;
use App\Models\ProviderCity;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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

            // Validation rules for user fields
            $userRules = [
                'name' => ['sometimes', 'string', 'max:255'],
                'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'unique:users,phone,' . $user->id],
                'bio' => ['sometimes', 'nullable', 'string'],
                'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
                'avatar' => ['sometimes', 'nullable', 'image|mimes:jpeg,jpg,png,gif,webp|max:5120'],
                'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            ];

            // Validation rules for provider profile (if user is provider)
            $providerRules = [];
            if ($user->isProvider()) {
                $providerRules = [
                    'provider.bio' => ['sometimes', 'nullable', 'string', 'max:1000'],
                    'provider.phone' => ['sometimes', 'nullable', 'string', 'max:20'],
                    'provider.address' => ['sometimes', 'nullable', 'string', 'max:500'],
                    'provider.working_hours' => ['sometimes', 'nullable', 'array'],
                    'provider.working_hours.*.day' => ['required_with:provider.working_hours', 'string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
                    'provider.working_hours.*.start' => ['required_with:provider.working_hours', 'string'],
                    'provider.working_hours.*.end' => ['required_with:provider.working_hours', 'string'],
                    'provider.working_hours.*.is_open' => ['sometimes', 'boolean'],
                ];
            }

            $allRules = array_merge($userRules, $providerRules);
            $validated = $request->validate($allRules);

            // Update user fields
            $userData = [];
            if (isset($validated['name'])) {
                $userData['name'] = $validated['name'];
            }
            if (isset($validated['phone'])) {
                $userData['phone'] = $validated['phone'];
            }
            if (isset($validated['bio'])) {
                $userData['bio'] = $validated['bio'];
            }
            if (isset($validated['email'])) {
                $userData['email'] = $validated['email'];
            }
            if (isset($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            // Update provider profile if user is provider
            if ($user->isProvider() && isset($validated['provider'])) {
                $provider = $user->providerProfile;

                if (!$provider) {
                    // Create provider profile if it doesn't exist
                    $provider = ProviderProfile::create([
                        'user_id' => $user->id,
                        'is_active' => true,
                        'is_verified' => false,
                        'max_categories' => 3,
                        'max_cities' => 5,
                    ]);
                }

                $providerData = [];
                if (isset($validated['provider']['bio'])) {
                    $providerData['bio'] = $validated['provider']['bio'];
                }
                if (isset($validated['provider']['phone'])) {
                    $providerData['phone'] = $validated['provider']['phone'];
                }
                if (isset($validated['provider']['address'])) {
                    $providerData['address'] = $validated['provider']['address'];
                }
                if (isset($validated['provider']['working_hours'])) {
                    $providerData['working_hours'] = $validated['provider']['working_hours'];
                }

                if (!empty($providerData)) {
                    $provider->update($providerData);
                }
            }

            // Reload user with relationships
            $user->load(['providerProfile']);

            Log::info('API User profile updated', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($userData),
            ]);

            return $this->success($user, 'تم تحديث الملف الشخصي بنجاح');
        }, 'حدث خطأ أثناء تحديث الملف الشخصي');
    }

    /**
     * إكمال الملف الشخصي
     * POST /api/v1/auth/complete-profile
     */
    public function completeProfile(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            /** @var User $user */
            $user = $request->user();

            // Validation rules
            $rules = [
                'user_type' => ['required', 'in:customer,provider'],
                'terms' => ['required', 'accepted'],
            ];

            // Additional rules for providers
            if ($request->input('user_type') === 'provider') {
                $maxCategories = SystemSetting::get('provider_max_categories', 3);
                $maxCities = SystemSetting::get('provider_max_cities', 5);

                $rules = array_merge($rules, [
                    'bio' => ['required', 'string', 'max:1000'],
                    'phone' => ['required', 'string', 'max:20'],
                    'address' => ['required', 'string', 'max:500'],
                    'categories' => ['required', 'array', 'min:1', 'max:' . $maxCategories],
                    'categories.*' => ['required', 'exists:categories,id'],
                    'cities' => ['required', 'array', 'min:1', 'max:' . $maxCities],
                    'cities.*' => ['required', 'exists:cities,id'],
                    'working_hours' => ['nullable', 'array'],
                    'working_hours.*.day' => ['required_with:working_hours', 'string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
                    'working_hours.*.start' => ['required_with:working_hours', 'string'],
                    'working_hours.*.end' => ['required_with:working_hours', 'string'],
                    'working_hours.*.is_open' => ['sometimes', 'boolean'],
                    'avatar' => ['nullable', 'image|mimes:jpeg,jpg,png,gif,webp|max:5120'],
                ]);
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();
            try {
                // Update user type and terms
                $user->update([
                    'user_type' => $validated['user_type'],
                    'terms_accepted_at' => now(),
                ]);

                // Handle provider profile
                if ($validated['user_type'] === 'provider') {
                    $maxCategories = SystemSetting::get('provider_max_categories', 3);
                    $maxCities = SystemSetting::get('provider_max_cities', 5);

                    // Handle avatar upload
                    $avatarPath = null;
                    if ($request->hasFile('avatar')) {
                        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                            Storage::disk('public')->delete($user->avatar);
                        }
                        $avatarPath = $request->file('avatar')->store('avatars', 'public');
                        $user->update(['avatar' => $avatarPath]);
                    }

                    // Update user phone if provided
                    if (isset($validated['phone'])) {
                        $user->update(['phone' => $validated['phone']]);
                    }

                    // Create or update provider profile
                    $profile = ProviderProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'bio' => $validated['bio'],
                            'phone' => $validated['phone'],
                            'address' => $validated['address'],
                            'working_hours' => $validated['working_hours'] ?? null,
                            'max_categories' => $maxCategories,
                            'max_cities' => $maxCities,
                            'is_verified' => SystemSetting::get('provider_auto_approve', false),
                            'is_active' => SystemSetting::get('provider_auto_approve', false),
                        ]
                    );

                    // Delete old categories and cities
                    $user->providerCategories()->delete();
                    $user->providerCities()->delete();

                    // Add new categories
                    foreach ($validated['categories'] as $categoryId) {
                        ProviderCategory::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                            'is_active' => true,
                        ]);
                    }

                    // Add new cities
                    foreach ($validated['cities'] as $cityId) {
                        ProviderCity::create([
                            'user_id' => $user->id,
                            'city_id' => $cityId,
                            'is_active' => true,
                        ]);
                    }
                }

                DB::commit();

                // Reload user with relationships
                $user->load(['providerProfile', 'providerCategories', 'providerCities']);

                Log::info('API User profile completed', [
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                ]);

                return $this->success($user, 'تم إكمال الملف الشخصي بنجاح');
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }, 'حدث خطأ أثناء إكمال الملف الشخصي');
    }
}
