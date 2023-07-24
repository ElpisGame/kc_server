<?php

namespace App\Http\Middleware\Filters;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestFilter
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::info("{$request->method()},{$request->fullUrl()}\n");
        $raw = $request->getContent();
        if ($raw) {
            Log::info("\n{$raw}\n");
        }
        $this->sqlDebug();
        return $next($request);
    }

    public function sqlDebug($ms = 0)
    {
        DB::listen(function ($query) use ($ms) {
            $tmp = str_replace('?', '"' . '%s' . '"', $query->sql);
            $qBindings = [];
            foreach ($query->bindings as $key => $value) {
                if (is_numeric($key)) {
                    $qBindings[] = $value;
                } else {
                    $tmp = str_replace(':' . $key, '"' . $value . '"', $tmp);
                }
            }
            $tmp = vsprintf($tmp, $qBindings);
            $tmp = str_replace("\\", "", $tmp);
            if ($ms) {
                if ($query->time < $ms) {
                    return;
                }
            }
            $info = 'time:' . $query->time . 'ms; ' . $tmp;
            Log::info('[SQL]', [$info]);
        });
    }
}
