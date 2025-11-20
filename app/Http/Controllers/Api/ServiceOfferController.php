<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use App\Models\ServiceOffer;
use Illuminate\Http\Request;

class ServiceOfferController extends BaseApiController
{
    public function index(Request $request)
    {
        $offers = ServiceOffer::query()
            ->with(['service:id,title,slug,user_id', 'provider:id,name,avatar'])
            ->when($request->user()->isProvider(), fn ($query) => $query->where('provider_id', $request->user()->id))
            ->when(!$request->user()->isProvider(), function ($query) use ($request) {
                $query->whereHas('service', fn ($serviceQuery) => $serviceQuery->where('user_id', $request->user()->id));
            })
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->success($offers);
    }

    public function store(Request $request, Service $service)
    {
        $user = $request->user();

        if (!$user->isProvider()) {
            return $this->error('فقط مزودو الخدمات يمكنهم تقديم عروض', 403);
        }

        if ($service->user_id === $user->id) {
            return $this->error('لا يمكنك تقديم عرض على خدمتك الخاصة', 422);
        }

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $existingOffer = ServiceOffer::where('service_id', $service->id)
            ->where('provider_id', $user->id)
            ->first();

        if ($existingOffer) {
            return $this->error('لقد قمت بتقديم عرض سابق لهذه الخدمة', 422);
        }

        $offer = ServiceOffer::create([
            'service_id' => $service->id,
            'provider_id' => $user->id,
            'price' => $data['price'],
            'notes' => $data['notes'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'status' => 'pending',
        ]);

        return $this->success($offer->load('provider:id,name,avatar'), 'تم تقديم العرض بنجاح', 201);
    }

    public function accept(ServiceOffer $offer, Request $request)
    {
        $serviceOwnerId = $offer->service->user_id;

        if ($serviceOwnerId !== $request->user()->id) {
            return $this->error('لا يمكنك تنفيذ هذا الإجراء', 403);
        }

        $offer->markAsAccepted();

        return $this->success($offer->fresh(), 'تم قبول العرض بنجاح');
    }

    public function reject(ServiceOffer $offer, Request $request)
    {
        $serviceOwnerId = $offer->service->user_id;

        if ($serviceOwnerId !== $request->user()->id) {
            return $this->error('لا يمكنك تنفيذ هذا الإجراء', 403);
        }

        $offer->update([
            'status' => 'rejected',
        ]);

        return $this->success($offer->fresh(), 'تم رفض العرض');
    }

    public function deliver(ServiceOffer $offer, Request $request)
    {
        if ($offer->provider_id !== $request->user()->id) {
            return $this->error('لا يمكنك تنفيذ هذا الإجراء', 403);
        }

        if (!$offer->canBeDelivered()) {
            return $this->error('لا يمكن تسليم هذا العرض حالياً', 422);
        }

        $offer->markAsDelivered();

        return $this->success($offer->fresh(), 'تم تحديد العرض كمُسلم');
    }

    public function review(ServiceOffer $offer, Request $request)
    {
        if ($offer->service->user_id !== $request->user()->id) {
            return $this->error('لا يمكنك تقييم هذا العرض', 403);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        if (!$offer->canBeRated()) {
            return $this->error('لا يمكن تقييم هذا العرض حالياً', 422);
        }

        $offer->addReview($data['rating'], $data['review'] ?? null);

        return $this->success($offer->fresh(), 'تم إضافة التقييم بنجاح');
    }
}


