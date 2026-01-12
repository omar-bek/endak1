<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use App\Models\ServiceOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ServiceOfferController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($request) {
            $offers = ServiceOffer::query()
                ->with(['service:id,title,slug,user_id', 'provider:id,name,avatar'])
                ->when($request->user()->isProvider(), fn ($query) => $query->where('provider_id', $request->user()->id))
                ->when(!$request->user()->isProvider(), function ($query) use ($request) {
                    $query->whereHas('service', fn ($serviceQuery) => $serviceQuery->where('user_id', $request->user()->id));
                })
                ->latest()
                ->paginate($request->get('per_page', 15));

            return $this->success($offers);
        }, 'حدث خطأ أثناء جلب العروض');
    }

    public function store(Request $request, Service $service)
    {
        return $this->executeApiWithTryCatch(function () use ($request, $service) {
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

            Log::info('API Service offer created', [
                'offer_id' => $offer->id,
                'service_id' => $service->id,
                'provider_id' => $user->id
            ]);

            return $this->success($offer->load('provider:id,name,avatar'), 'تم تقديم العرض بنجاح', 201);
        }, 'حدث خطأ أثناء تقديم العرض');
    }

    public function accept(ServiceOffer $offer, Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($offer, $request) {
            $serviceOwnerId = $offer->service->user_id;

            if ($serviceOwnerId !== $request->user()->id) {
                return $this->error('لا يمكنك تنفيذ هذا الإجراء', 403);
            }

            $offer->markAsAccepted();

            Log::info('API Service offer accepted', [
                'offer_id' => $offer->id,
                'service_id' => $offer->service_id
            ]);

            return $this->success($offer->fresh(), 'تم قبول العرض بنجاح');
        }, 'حدث خطأ أثناء قبول العرض');
    }

    public function reject(ServiceOffer $offer, Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($offer, $request) {
            $serviceOwnerId = $offer->service->user_id;

            if ($serviceOwnerId !== $request->user()->id) {
                return $this->error('لا يمكنك تنفيذ هذا الإجراء', 403);
            }

            $offer->update([
                'status' => 'rejected',
            ]);

            Log::info('API Service offer rejected', [
                'offer_id' => $offer->id,
                'service_id' => $offer->service_id
            ]);

            return $this->success($offer->fresh(), 'تم رفض العرض');
        }, 'حدث خطأ أثناء رفض العرض');
    }

    public function deliver(ServiceOffer $offer, Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($offer, $request) {
            if ($offer->provider_id !== $request->user()->id) {
                return $this->error('لا يمكنك تنفيذ هذا الإجراء', 403);
            }

            if (!$offer->canBeDelivered()) {
                return $this->error('لا يمكن تسليم هذا العرض حالياً', 422);
            }

            $offer->markAsDelivered();

            Log::info('API Service offer delivered', [
                'offer_id' => $offer->id,
                'service_id' => $offer->service_id
            ]);

            return $this->success($offer->fresh(), 'تم تحديد العرض كمُسلم');
        }, 'حدث خطأ أثناء تسليم العرض');
    }

    public function review(ServiceOffer $offer, Request $request)
    {
        return $this->executeApiWithTryCatch(function () use ($offer, $request) {
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

            Log::info('API Service offer reviewed', [
                'offer_id' => $offer->id,
                'rating' => $data['rating']
            ]);

            return $this->success($offer->fresh(), 'تم إضافة التقييم بنجاح');
        }, 'حدث خطأ أثناء إضافة التقييم');
    }
}








