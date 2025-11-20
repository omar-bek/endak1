<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    public function index(Request $request)
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->get('per_page', 20));

        return $this->success([
            'notifications' => $notifications,
            'unread_count' => Notification::getUnreadCountForUser($request->user()->id),
        ]);
    }

    public function markAsRead(Notification $notification, Request $request)
    {
        $this->authorizeNotificationOwner($notification, $request->user()->id);
        $notification->markAsRead();

        return $this->success($notification->fresh(), 'تم تعليم الإشعار كمقروء');
    }

    public function markAllAsRead(Request $request)
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->success(null, 'تم تعليم جميع الإشعارات كمقروءة');
    }

    public function destroy(Notification $notification, Request $request)
    {
        $this->authorizeNotificationOwner($notification, $request->user()->id);
        $notification->delete();

        return $this->success(null, 'تم حذف الإشعار بنجاح');
    }

    private function authorizeNotificationOwner(Notification $notification, int $userId): void
    {
        if ($notification->user_id !== $userId) {
            abort(403, 'لا يمكنك تنفيذ هذا الإجراء');
        }
    }
}


