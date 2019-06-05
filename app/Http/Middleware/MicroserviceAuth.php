<?php

namespace App\Http\Middleware;

use App\Models\App;
use function App\Utils\microservice_access_decode;
use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MicroserviceAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->auth($request);
        // 执行动作 之前
        $response = $next($request);
        // 执行动作 之后
        return $response;
    }

    private function auth($request)
    {
        $access = microservice_access_decode($request->header('access'));
        $access_decode = null;
        if($access and $access['appkey'] and $access['appsecret'])
        {
            $app = App::where('appkey',$access['appkey'])->where('appsecret',$access['appsecret'])->select('id','name','slug')->first();
            $access_decode['app'] = $app;
            $access_decode['info'] = $access['info'];
            $access_decode['time'] = $access['time'];
            session()->put('access', $access_decode);
        }else{
            session()->put('access', null);
            throw new HttpException(401, 'Access Error !');
        }
    }
}
