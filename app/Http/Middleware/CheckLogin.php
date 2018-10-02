<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $auth;
    public function  __construct()
    {
        $this->auth =  Auth::guard('admin_api');
    }


    public function handle($request, Closure $next)
    {

        if (! $this->auth->parser()->setRequest($request)->hasToken()) {
            throw new UnauthorizedHttpException('jwt-auth', 'Token not provided');
        }
        // # 过滤内网
        // $ip = $request->getClientIp();
        // # 获取IP白名单
        // $white_list = explode(',', env('WHITE_HOST'));
        // if (!in_array($ip, $white_list)) {
        //     return Responser::error(403);
        // }
        try {
            $token = $this->auth->setRequest($request)->getToken();
             $user = $this->auth->parseToken()->authenticate();
            $user = $this->auth->toUser($token);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return Responser::error(402);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            try {
                $token = $this->auth->getToken()->get();//验证是否能获取到token
                $newToken = auth()->refresh();
            } catch (\Exception $e) {
                return Responser::error($e->getMessage());
            }
            #刷新token并且返回新token
            return Responser::error(406,[
                'newToken' => $newToken
            ]);
        } catch (JWTException $e) {
            return Responser::error(402);
        }

        dd('66');

        if (!$request->user()) {
            return redirect('/'.(empty(config('webset.webdir'))?'admin':config('webset.webdir')).'/login');
        }
        return $next($request);
    }


}
