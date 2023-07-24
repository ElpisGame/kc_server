<?php

namespace App\Http\Controllers\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WechatRefundResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
