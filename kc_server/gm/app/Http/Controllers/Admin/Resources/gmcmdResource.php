<?php

namespace App\Http\Controllers\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class gmcmdResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
