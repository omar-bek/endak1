<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends BaseApiController
{
    public function index(Request $request)
    {
        $services = Service::query()
            ->where('is_active', true)
            ->with(['category:id,name,slug', 'subCategory:id,name_ar,name_en', 'city:id,name_ar,name_en', 'user:id,name,avatar'])
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('sub_category_id'), fn ($query) => $query->where('sub_category_id', $request->integer('sub_category_id')))
            ->when($request->filled('city_id'), fn ($query) => $query->where('city_id', $request->integer('city_id')))
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->get('search');
                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate($request->get('per_page', 12));

        return $this->success($services);
    }

    public function show(Service $service)
    {
        $service->load([
            'category:id,name,slug',
            'subCategory:id,name_ar,name_en',
            'city:id,name_ar,name_en',
            'user:id,name,avatar',
            'offers.provider:id,name,avatar',
        ]);

        return $this->success($service);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'custom_fields' => ['nullable', 'array'],
        ]);

        $data['user_id'] = $request->user()->id;
        $data['is_active'] = true;
        $data['custom_fields'] = $data['custom_fields'] ?? [];

        $service = Service::create($data);

        return $this->success($service->fresh(), 'تم إنشاء الخدمة بنجاح', 201);
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeServiceOwner($request->user()->id, $service);

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'sub_category_id' => ['sometimes', 'nullable', 'exists:sub_categories,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'custom_fields' => ['sometimes', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service->update($data);

        return $this->success($service->fresh(), 'تم تحديث الخدمة بنجاح');
    }

    public function destroy(Request $request, Service $service)
    {
        $this->authorizeServiceOwner($request->user()->id, $service);
        $service->delete();

        return $this->success(null, 'تم حذف الخدمة بنجاح');
    }

    public function myServices(Request $request)
    {
        $services = $request->user()
            ->services()
            ->with(['category:id,name', 'city:id,name_ar'])
            ->latest()
            ->paginate($request->get('per_page', 12));

        return $this->success($services);
    }

    private function authorizeServiceOwner(int $userId, Service $service): void
    {
        if ($service->user_id !== $userId) {
            abort(403, 'لا يمكنك تعديل هذه الخدمة');
        }
    }
}

