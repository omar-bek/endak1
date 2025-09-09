<?php

namespace App\Http\Controllers;

use App\Models\Notification as CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * عرض جميع إشعارات المستخدم
     */
    public function index()
    {
        // استخدام custom notifications
        $notifications = CustomNotification::where('user_id', Auth::id())
                                         ->orderBy('created_at', 'desc')
                                         ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead($id)
    {
        $notification = CustomNotification::where('user_id', Auth::id())
                                        ->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        CustomNotification::where('user_id', Auth::id())
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }


    /**
     * حذف إشعار
     */
    public function destroy($id)
    {
        $notification = CustomNotification::where('user_id', Auth::id())
                                        ->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'تم حذف الإشعار بنجاح');
    }

    /**
     * الحصول على الإشعارات غير المقروءة (لـ AJAX)
     */
    public function getUnread()
    {
        $notifications = Auth::user()->unread_notifications;
        $count = Auth::user()->unread_notifications_count;

        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }
}
