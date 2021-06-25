<?php

namespace App;

use Config;
use Illuminate\Database\Eloquent\Model;

class ClientSetting extends Model
{
    protected $table = 'client_settings';

    protected $guarded = [];

    /**
     * @param $key
     */
    public static function get($key)
    {
        $setting = new self();
        $entry = $setting->where('key', $key)->first();
        if (!$entry) {
            return;
        }
        return $entry->value;
    }
    /**
     * @param $key
     * @param null $value
     * @return bool
     */
    public static function set($key, $value = null)
    {
        $setting = new self();
        $entry = $setting->where('key', $key)->firstOrFail();
        $entry->value = $value;
        $entry->saveOrFail();
        Config::set('key', $value);
        if (Config::get($key) == $value) {
            return true;
        }
        return false;
    }

    public function company()
    {
         return $this->belongsTo('App\Company');
    }

    public function client_outlet_settings()
    {
        return $this->hasOne('App\ClientOutletSetting', 'company_id', 'company_id');
    }
}