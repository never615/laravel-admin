<?php

namespace Encore\Admin\Middleware;

use Closure;
use Encore\Admin\Auth\Database\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * 自动处理权限
 * 原理:
 *   使用路由名,作为权限名.然后在中间件中判断是否拥有此权限
 * Class AutoPermissionMiddleware
 * @package Encore\Admin\Middleware
 */
class AutoPermissionMiddleware
{
    protected $except = [
    ];

    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //如果是项目拥有者不管他
        //如果权限表里面没有这个权限,则放行
        //如果权限表有这个权限,且当前用户有该权限则放行,如果当前用户没有则提示权限不足

        $previousUrl = URL::previous();
        $currentUrl = $request->path();
        $currentUrl=substr($currentUrl, 5);  //admin
        $currentRouteName = Route::currentRouteName();

        if (Auth::guard("admin")->user()->inRoles(config('admin.roles.owner'))) {
            //pass
            return $next($request);
        } else {
            $permission = Permission::where('slug', $currentUrl)->orWhere('slug', $currentRouteName)->first();
            if ($permission) {
                if (Auth::guard("admin")->user()->can($permission->slug)) {
                    //有这个权限 pass
                    return $next($request);
                } else {
                    //denied
                    if ($request->expectsJson()) {
                        throw new AccessDeniedHttpException(trans("errors.forbidden"));
                    } else {
                        return response()->view('errors.403', compact('previousUrl'));
                    }
                }
            } else {
                //没有这个权限,但是也没有创建这个权限,删除
                return $next($request);
            }
        }
    }
}
