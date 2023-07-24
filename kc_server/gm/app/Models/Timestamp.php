<?php
namespace App\Models;

trait Timestamp{

    public function getCreatedAtAttribute($rawTime){
        return $this->changeTimeZone($rawTime, '',config('app.timezone'));
    }

    public function getUpdatedAtAttribute($rawTime){
        return $this->changeTimeZone($rawTime, '',config('app.timezone'));
    }

    public function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null){

        if (empty($timeZoneSource)) {
            $timeZoneSource = date_default_timezone_get();
        }
        if (empty($timeZoneTarget)) {
            $timeZoneTarget = date_default_timezone_get();
        }

        $dt = new \DateTime($dateString, new \DateTimeZone($timeZoneSource));
        $dt->setTimezone(new \DateTimeZone($timeZoneTarget));
        return $dt->format('Y-m-d H:i:s');
    }
}
