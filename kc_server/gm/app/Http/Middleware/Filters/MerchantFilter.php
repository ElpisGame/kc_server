<?php


namespace App\Http\Middleware\Filters;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class MerchantFilter
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();
        if ($user && $user->is_merchant == 1) {
            return $next($request);
        } else {
            throw new \Exception("User is not merchant");
        }
    }
}
