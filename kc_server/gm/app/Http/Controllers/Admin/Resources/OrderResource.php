<?php

namespace App\Http\Controllers\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
