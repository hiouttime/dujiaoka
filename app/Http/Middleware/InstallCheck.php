<?php

namespace App\Http\Middleware;

use Closure;

class InstallCheck
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
        // 检查是否已安装 - 已安装则阻止访问安装页面
        $installLock = base_path() . DIRECTORY_SEPARATOR . 'install.lock';
        if (file_exists($installLock)) {
            return redirect(url('/'));
        }
        return $next($request);
    }
}
