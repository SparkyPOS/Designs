<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function notifications(){
        $notifications = UserNotification::where('user_id', auth()->id())->orderBy('id','desc')->paginate(getPaginate());
        $hasUnread = UserNotification::where('user_id', auth()->id())->where('is_read',Status::NO)->exists();
        $pageTitle = 'Notifications';
        return view('Template::user.notifications',compact('pageTitle','notifications','hasUnread'));
    }


    public function notificationRead($id){
        $notification = UserNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function readAllNotification(){
        UserNotification::where('user_id', auth()->id())->where('is_read',Status::NO)->update([
            'is_read'=>Status::YES
        ]);
        $notify[] = ['success','Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function deleteAllNotification(){
        UserNotification::where('user_id', auth()->id())->delete();
        $notify[] = ['success','Notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteSingleNotification($id){
        UserNotification::where('user_id', auth()->id())->where('id',$id)->delete();
        $notify[] = ['success','Notification deleted successfully'];
        return back()->withNotify($notify);
    }
}
