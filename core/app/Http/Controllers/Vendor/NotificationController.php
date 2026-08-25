<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\Status;
use App\Models\VendorNotification;
use App\Http\Controllers\Controller;

class NotificationController extends Controller {
    public function notifications() {
        $notifications = VendorNotification::where('vendor_id', authVendorId())->orderBy('id', 'desc')->paginate(getPaginate());
        $hasUnread = VendorNotification::where('vendor_id', authVendorId())->where('is_read', Status::NO)->exists();
        $pageTitle = 'Notifications';
        return view('Template::vendor.notifications', compact('pageTitle', 'notifications', 'hasUnread'));
    }

    public function notificationRead($id) {
        $notification = VendorNotification::where('vendor_id', authVendorId())->findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function readAllNotification() {
        VendorNotification::where('vendor_id', authVendorId())->where('is_read', Status::NO)->update([
            'is_read' => Status::YES
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function deleteAllNotification() {
        VendorNotification::where('vendor_id', authVendorId())->delete();
        $notify[] = ['success', 'Notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteSingleNotification($id) {
        VendorNotification::where('vendor_id', authVendorId())->where('id', $id)->delete();
        $notify[] = ['success', 'Notification deleted successfully'];
        return back()->withNotify($notify);
    }
}
