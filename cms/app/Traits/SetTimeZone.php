<?php


namespace App\Traits;

trait SetTimeZone
{
    public $tz = 'UTC';
    public function getTz()
    {
        $this->tz = config('settings.time_zone');
    }

    public function getCreatedAtAttribute($value)
    {
        $date = new DateTime($value);
        $date->setTimezone(new DateTimeZone($this->tz));

        return $date->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = new DateTime($value);
        $date->setTimezone(new DateTimeZone($this->tz));

        return $date->format('Y-m-d H:i:s');
    }
}