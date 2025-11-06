<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'profile', 'updateProfile', 'saveUserType', 'showCompleteProfile']);
    }

    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // التحقق من الإيميل قبل محاولة تسجيل الدخول
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // التحقق من تحقق الإيميل
            if (!$user->hasVerifiedEmail()) {
                return back()->withErrors([
                    'email' => 'يجب التحقق من الإيميل أولاً. تحقق من بريدك الإلكتروني للحصول على رابط التحقق.'
                ])->onlyInput('email');
            }

            // إذا كان الإيميل محققاً، قم بتسجيل الدخول
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'الإيميل أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    /**
     * عرض صفحة التسجيل
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * معالجة التسجيل بالإيميل والهاتف
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^\d{4}/', // يجب أن تبدأ بأربعة أرقام
            ],
            'user_type' => 'required|in:customer,provider',
            'terms' => 'required|accepted',
        ], [
            'password.regex' => 'كلمة المرور يجب أن تبدأ بأربعة أرقام على الأقل.',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'terms_accepted_at' => now(), // الموافقة على الشروط عند التسجيل
        ]);

        // Send email verification notification
        event(new Registered($user));

        // Login user temporarily to show verification notice
        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'تم إنشاء الحساب بنجاح! يرجى التحقق من الإيميل لإكمال التسجيل.');
    }


    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * عرض الملف الشخصي
     */
    public function profile()
    {
        $user = Auth::user();

        // إذا كان مزود خدمة، توجيه إلى صفحة مزود الخدمة
        if ($user->isProvider()) {
            return redirect()->route('provider.profile');
        }

        return view('profile', compact('user'));
    }

    /**
     * عرض صفحة إتمام الملف الشخصي (اختيار الدور والموافقة على الشروط)
     */
    public function showCompleteProfile()
    {
        $user = Auth::user();

        // إذا كان المستخدم قد اختار الدور ووافق على الشروط، لا حاجة لهذه الصفحة
        if ($user->user_type && $user->terms_accepted_at) {
            return redirect('/');
        }

        return view('auth.complete-profile');
    }

    /**
     * معالجة إتمام الملف الشخصي
     */
    // public function completeProfile(Request $request)
    // {
    //     try {
    //         $user = Auth::user();

    //         if (!$user) {
    //             if ($request->ajax() || $request->wantsJson()) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'يجب تسجيل الدخول أولاً'
    //                 ], 401);
    //             }
    //             return redirect()->route('login');
    //         }

    //         $request->validate([
    //             'user_type' => 'required|in:customer,provider',
    //             'terms' => 'required|accepted',
    //         ], [
    //             'user_type.required' => 'يجب اختيار نوع الحساب',
    //             'user_type.in' => 'نوع الحساب غير صحيح',
    //             'terms.required' => 'يجب الموافقة على الشروط والأحكام',
    //             'terms.accepted' => 'يجب الموافقة على الشروط والأحكام',
    //         ]);

    //         $user->user_type = $request->user_type;
    //         $user->terms_accepted_at = now();
    //         $user->save();

    //         // Regenerate session to prevent CSRF token issues
    //         $request->session()->regenerate();

    //         // Remove session flag
    //         session()->forget('show_user_type_modal');

    //         // Return JSON response if AJAX request (from modal), otherwise redirect
    //         if ($request->ajax() || $request->wantsJson()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'تم إتمام الملف الشخصي بنجاح! مرحباً بك في إنداك'
    //             ]);
    //         }

    //         return redirect('/')->with('success', 'تم إتمام الملف الشخصي بنجاح! مرحباً بك في إنداك');
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         if ($request->ajax() || $request->wantsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'التحقق من البيانات فشل',
    //                 'errors' => $e->errors()
    //             ], 422);
    //         }
    //         return back()->withErrors($e->errors())->withInput();
    //     } catch (\Exception $e) {
    //         Log::error('Complete profile error: ' . $e->getMessage());

    //         if ($request->ajax() || $request->wantsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'حدث خطأ أثناء إتمام الملف الشخصي: ' . $e->getMessage()
    //             ], 500);
    //         }

    //         return back()->withErrors(['error' => 'حدث خطأ أثناء إتمام الملف الشخصي. يرجى المحاولة مرة أخرى.'])->withInput();
    //     }
    // }
    public function saveUserType(Request $request)
    {
        try {
            $request->validate([
                'user_type' => 'required|in:customer,provider',
                'terms' => 'required|accepted',
            ], [
                'user_type.required' => 'يجب اختيار نوع الحساب',
                'user_type.in' => 'قيمة غير صالحة لنوع الحساب',
                'terms.required' => 'يجب الموافقة على الشروط والأحكام',
                'terms.accepted' => 'يجب الموافقة على الشروط والأحكام',
            ]);

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            $userType = $request->input('user_type');

            // Use DB::table()->update() to ensure it's saved
            $affected = DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'user_type' => $userType,
                    'terms_accepted_at' => now(),
                    'updated_at' => now()
                ]);

            // Refresh user model
            $user->refresh();

            // Remove session flag
            session()->forget('show_user_type_modal');

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث نوع الحساب بنجاح',
                'user_type' => $user->user_type
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'التحقق من البيانات فشل',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Save user type error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'bio']);

        // رفع الصورة الشخصية
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('users', 'public');
            $data['image'] = $imagePath;
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
}
