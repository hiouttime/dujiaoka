<?php

namespace App\Http\Middleware;

use Closure;

class Challenge
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
        $status = session('challenge');
        if($status === "pass")
            return $next($request);

        if(isset($_REQUEST['_challenge'])){
            if (substr(sha1($_REQUEST['_challenge']), -4) === $status){
                session(['challenge' => 'pass']);
                return $next($request);
            }
        }
        $challenge = substr(sha1(rand()), -4);
        session(['challenge' => $challenge]);
        return response()->view('common/challenge',['code' => $challenge]);
    }
}
