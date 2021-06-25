<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Notification;
use Illuminate\Support\Facades\Session;
Use Auth;
use DB;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function index(Request $request)
{
    $company_id = config('settings.company_id');
    $notifications = Notification::where('notifications.company_id', $company_id)
        ->join('employees', 'employees.id', 'notifications.employee_id')
        ->where('notifications.to', 0)
        ->orderBy('notifications.created_at', 'desc')
        ->select(['notifications.*', 'employees.name', 'employees.image', 'employees.image_path', 'employees.gender'])
        ->paginate(10);
    $notificationIds = [];
    foreach ($notifications as &$notification) {
        array_push($notificationIds, $notification->id);
        if ($notification->data_type == 'noorders')
            $notification->data_type = '';
        elseif (empty($notification->data_type) && !empty($notification->title)) {
            if (similar_text($notification->title, 'order') > 3)
                $notification->data_type = 'order';
            elseif (similar_text($notification->title, 'leave') > 3)
                $notification->data_type = 'leave';
            elseif (similar_text($notification->title, 'collection') > 3)
                $notification->data_type = 'collection';
            elseif (similar_text($notification->title, 'announcement') > 3)
                $notification->data_type = 'announcement';
            elseif (similar_text($notification->title, 'expense') > 3)
                $notification->data_type = 'expense';
            elseif (similar_text($notification->title, 'meeting') > 3)
                $notification->data_type = 'meeting';
            elseif (similar_text($notification->title, 'remark') > 3)
                $notification->data_type = 'remark';
            elseif (similar_text($notification->title, 'task') > 3)
                $notification->data_type = 'task';
            elseif (similar_text($notification->title, 'party') > 3)
                $notification->data_type = 'client';
            elseif (similar_text($notification->title, 'activities') > 3)
                $notification->data_type = 'activities';
            elseif (similar_text($notification->title, 'beatplan') > 3)
                $notification->data_type = 'beatplan';
            else
                $notification->data_type = '';
        }
    }

    Notification::where('company_id', $company_id)->whereIn('id', $notificationIds)->update(['status' => 2]);
    return view('company.notifications.index', compact('notifications'));
}

    public function getUserNotification(Request $request)
    {
        $company_id = config('settings.company_id');
        $objNotification = Notification::getInstance();
        $notificationData = $objNotification->getAllNotification($company_id);

        if (!empty($notificationData))
            Session::put('latestNotificationDate', date('Y-m-d H:i:s', strtotime($notificationData[0]['created_at'])));
        else
            Session::put('latestNotificationDate', date('Y-m-d H:i:s'));

        $count = Notification::where('notifications.company_id', $company_id)
            ->where('notifications.to', 0)
            ->where('notifications.status', 1)
            ->count();
        return json_encode(compact('notificationData', 'count'));
    }

    public function getMoreUserNotification(Request $request)
    {
        $company_id = config('settings.company_id');
        $objNotification = Notification::getInstance();
        $notificationData = $objNotification->getAllNotification($company_id, Session::get('latestNotificationOffset') + 10);
        sleep(2);
        return json_encode($notificationData);
    }

    public function getUserLatestNotification(Request $request)
    {
        $objNotification = Notification::getInstance();
        $company_id = config('settings.company_id');
        $notificationData = $objNotification->getLatestNotification($company_id);
        if (!empty($notificationData))
            Session::put('latestNotificationDate', date('Y-m-d H:i:s', strtotime($notificationData[0]['created_at'])));
        else
            Session::put('latestNotificationDate', date('Y-m-d H:i:s'));
        return json_encode($notificationData);
    }

    public function notificationDetail(Request $request)
    {
        $company_id = config('settings.company_id');
        $notification = Notification::find($request->id);
        if ($notification->company_id == $company_id) {
            $notification->status = 2;
            $notification->save();
            $data = json_decode($notification->data);
            if($notification->data_type!='remark'){
              return redirect()->intended(domain_route('company.admin.' . $notification->data_type . '.show', [$data->id]));
            }else{
              return redirect()->intended(domain_route('company.admin.' . $notification->data_type . '.show', [$data->id]));
            }
        } else {
            return redirect()->intended(route('company.admin.home', ['domain' => domain()]));
        }
    }


}
