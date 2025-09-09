<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('provider');
    }

    /**
     * عرض جميع خدمات مزود الخدمة
     */
    public function index()
    {
        $services = auth()->user()->services()->with('category')->latest()->paginate(10);

        return view('provider.services.index', compact('services'));
    }

    /**
     * عرض نموذج إنشاء خدمة جديدة
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return view('provider.services.create', compact('categories'));
    }

    /**
     * حفظ خدمة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'is_featured' => 'boolean',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['is_active'] = true;

        // إنشاء slug فريد
        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;

        // التأكد من أن slug فريد
        while (Service::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $slug;

        // رفع الصورة
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        Service::create($data);

        return redirect()->route('provider.services.index')
                         ->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    /**
     * عرض نموذج تعديل خدمة
     */
    public function edit(Service $service)
    {
        // التأكد من أن الخدمة تخص المستخدم الحالي
        if ($service->user_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::where('is_active', true)->get();

        return view('provider.services.edit', compact('service', 'categories'));
    }

    /**
     * تحديث خدمة
     */
    public function update(Request $request, Service $service)
    {
        // التأكد من أن الخدمة تخص المستخدم الحالي
        if ($service->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'is_featured' => 'boolean',
        ]);

        $data = $request->all();

        // إنشاء slug فريد إذا تغير العنوان
        if ($request->title !== $service->title) {
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;

            // التأكد من أن slug فريد
            while (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;
        }

        // رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        $service->update($data);

        return redirect()->route('provider.services.index')
                         ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * حذف خدمة
     */
    public function destroy(Service $service)
    {
        // التأكد من أن الخدمة تخص المستخدم الحالي
        if ($service->user_id !== auth()->id()) {
            abort(403);
        }

        // حذف الصورة
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('provider.services.index')
                         ->with('success', 'تم حذف الخدمة بنجاح');
    }

    /**
     * تغيير حالة الخدمة
     */
    public function toggleStatus(Service $service)
    {
        // التأكد من أن الخدمة تخص المستخدم الحالي
        if ($service->user_id !== auth()->id()) {
            abort(403);
        }

        $service->update(['is_active' => !$service->is_active]);

        $status = $service->is_active ? 'تفعيل' : 'إلغاء تفعيل';

        return redirect()->route('provider.services.index')
                         ->with('success', "تم $status الخدمة بنجاح");
    }
}
