<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BaseModel;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

class Challenge
{
    private $whiteClass = [
        "App\Http\Controllers\PayController"
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        $routeAction = $request->route()->getAction();
        $controller = $routeAction['controller'] ?? null;
        if($controller && in_array(explode('@', $controller)[0], $this->whiteClass))
            return $next($request);
        
        if(cfg('is_cn_challenge') == BaseModel::STATUS_CLOSE)
            return $next($request);
            
        $status = session('challenge');
        if($status === "pass")
            return $next($request);

        if(isset($_REQUEST['_challenge'])){
            if (substr(sha1($_REQUEST['_challenge']), -4) == $status){
                session(['challenge' => 'pass']);
                return $next($request);
            }
        }
        
        $isoCode = get_ip_country($request->ip());
        if($isoCode != 'CN'){
            session(['challenge' => 'pass']);
            return $next($request);
        }
        $challenge = substr(sha1(rand()), -4);
        session(['challenge' => $challenge]);
        return response()->view('common/challenge',['code' => $challenge]);
    }
}
