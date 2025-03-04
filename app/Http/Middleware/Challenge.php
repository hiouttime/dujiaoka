<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BaseModel;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

class Challenge
{
    private $whiteClass = [
        "App\Http\Controllers\Pay"
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
        
        if(in_array($request->route()->getAction()["namespace"],$this->whiteClass))
            return $next($request);
        
        if(dujiaoka_config_get('is_cn_challenge') == BaseModel::STATUS_CLOSE)
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
