<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // التحقق من أن الإشعار مقروء
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    // تحديد الإشعار كمقروء
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    // تحديد الإشعار كغير مقروء
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    // الحصول على أيقونة الإشعار
    public function getIconAttribute()
    {
        $icons = [
            'offer_received' => 'fas fa-handshake text-success',
            'offer_accepted' => 'fas fa-check-circle text-success',
            'offer_rejected' => 'fas fa-times-circle text-danger',
            'service_requested' => 'fas fa-concierge-bell text-primary',
            'payment_received' => 'fas fa-money-bill-wave text-success',
            'service_completed' => 'fas fa-flag-checkered text-success',
            'system' => 'fas fa-info-circle text-info',
        ];

        return $icons[$this->type] ?? 'fas fa-bell text-warning';
    }

    // الحصول على لون الإشعار
    public function getColorAttribute()
    {
        $colors = [
            'offer_received' => 'success',
            'offer_accepted' => 'success',
            'offer_rejected' => 'danger',
            'service_requested' => 'primary',
            'payment_received' => 'success',
            'service_completed' => 'success',
            'system' => 'info',
        ];

        return $colors[$this->type] ?? 'warning';
    }

    // الحصول على الإشعارات غير المقروءة للمستخدم
    public static function getUnreadForUser($userId)
    {
        return static::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    // الحصول على عدد الإشعارات غير المقروءة للمستخدم
    public static function getUnreadCountForUser($userId)
    {
        return static::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->count();
    }

    // إنشاء إشعار جديد
    public static function createNotification($userId, $type, $title, $message, $data = null)
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

        // إنشاء إشعار عند تقديم عرض
    public static function createOfferReceivedNotification($serviceOffer)
    {
        $service = $serviceOffer->service;
        $provider = $serviceOffer->provider;

        return static::createNotification(
            $service->user_id,
            'offer_received',
            __('messages.offer_received_title'),
            __('messages.offer_received_message', [
                'provider' => $provider->name,
                'service' => $service->title
            ]),
            [
                'service_id' => $service->id,
                'offer_id' => $serviceOffer->id,
                'provider_id' => $provider->id,
                'price' => $serviceOffer->price,
            ]
        );
    }

    // إنشاء إشعار عند قبول العرض
    public static function createOfferAcceptedNotification($serviceOffer)
    {
        $service = $serviceOffer->service;
        $customer = $service->user;

        return static::createNotification(
            $serviceOffer->provider_id,
            'offer_accepted',
            __('messages.offer_accepted_title'),
            __('messages.offer_accepted_message', [
                'customer' => $customer->name,
                'service' => $service->title
            ]),
            [
                'service_id' => $service->id,
                'offer_id' => $serviceOffer->id,
                'customer_id' => $customer->id,
            ]
        );
    }

    // إنشاء إشعار عند رفض العرض
    public static function createOfferRejectedNotification($serviceOffer)
    {
        $service = $serviceOffer->service;
        $customer = $service->user;

        return static::createNotification(
            $serviceOffer->provider_id,
            'offer_rejected',
            __('messages.offer_rejected_title'),
            __('messages.offer_rejected_message', [
                'customer' => $customer->name,
                'service' => $service->title
            ]),
            [
                'service_id' => $service->id,
                'offer_id' => $serviceOffer->id,
                'customer_id' => $customer->id,
            ]
        );
    }
}
