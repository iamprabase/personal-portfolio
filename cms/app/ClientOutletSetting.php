<?php

namespace App;

use Config;
use Illuminate\Database\Eloquent\Model;

class ClientOutletSetting extends Model
{
    protected $table = 'client_outlet_settings';

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

    public function client_settings()
    {
      return $this->belongsTo('App\ClientSetting', 'company_id', 'company_id');
    }
}