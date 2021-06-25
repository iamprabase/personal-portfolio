<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Notification extends Model
{

    protected $table = 'notifications';

    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance))
            self::$instance = new Notification();
        return self::$instance;
    }

    public function getAllNotification($company_id, $offset = 0)
    {
        try {
            Session::put('latestNotificationOffset', $offset);
            $date = date('Y-m-d H:i:s');

            $notification = Notification::where('notifications.company_id', $company_id)
                ->join('employees', 'employees.id', 'notifications.employee_id')
                ->where('notifications.to', 0)
                ->where('notifications.status', 1)
                ->where('notifications.created_at', '<=', $date)
                ->orderBy('notifications.created_at', 'desc')
                ->offset($offset)
                ->limit(10)
                ->get(['notifications.*', 'employees.name', 'employees.gender', 'employees.image_path']);
            foreach ($notification as $key => $value) {
                $notification[$key]->date = $value->created_at->diffForHumans();
            }

            if (!empty($notification))
                return $notification->toArray();
            else
                return array();
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getLatestNotification($company_id)
    {
        try {
            $notification = Notification::where('notifications.company_id', $company_id)
                ->join('employees', 'employees.id', 'notifications.employee_id')
                ->where('notifications.to', 0)
                ->where('notifications.status', 1)
                ->orderBy('notifications.created_at', 'desc')
                ->where('notifications.created_at', '>', Session::get('latestNotificationDate'))
                ->get(['notifications.*', 'employees.name', 'employees.gender', 'employees.image_path']);

            foreach ($notification as $key => $value) {
                    $notification[$key]->date = $value->created_at->diffForHumans();
            }

            if (!empty($notification))
                return $notification->toArray();
            else
                return array();

        } catch (\Exception $e) {
            return array();
        }
    }
}
