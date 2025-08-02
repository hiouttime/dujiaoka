<?php

namespace App\Http\Middleware;

use App\Providers\AppServiceProvider;
use Closure;

class DujiaoSystem
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 检测https - 已迁移到 Filament，不再需要 admin 配置
        // if ($request->getScheme() == 'https') {
        //     $httpsConfig = [
        //         'https' => true
        //     ];
        //     config([
        //         'admin'  =>  array_merge(config('admin'), $httpsConfig)
        //     ]);
        //     (new AppServiceProvider(app()))->register();
        // }
        return $next($request);
    }
}
