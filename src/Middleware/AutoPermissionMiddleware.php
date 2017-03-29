<?php
/**
 * Created by PhpStorm.
 * User: never615
 * Date: 10/03/2017
 * Time: 8:36 PM
 *
 * You need set permission's slug by routeName or url( auth/roles of https://xxx.com/admin/auth/roles )
 */
namespace Encore\Admin\Middleware;

use Closure;
use Encore\Admin\Auth\Database\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class AutoPermissionMiddleware
 *
 *
 *
 * @package Encore\Admin\Middleware
 */
class AutoPermissionMiddleware
{
    protected $except = [
    ];

    /**
     * Handle an incoming request.
     *
     * @param         $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


//        $currentUrl = $request->path();
//        $currentUrl = substr($currentUrl, 6);  //admin
        $currentRouteName = Route::currentRouteName();
        $routenameArr = explode(".", $currentRouteName);

        if (Auth::guard("admin")->user()->isOwner()) {
            //pass
            return $next($request);
        } else {
            //1.检查当前路由在权限列表中有没有对应的权限,如果没有设置该功能对应的权限,则默认通过
            $permission = Permission::where('slug', $currentRouteName)->first();
            if ($permission) {
                //权限管理有该权限,检查用户是否有该权限
                if (Auth::guard("admin")->user()->can($permission->slug)) {
                    //pass
                    return $next($request);
                } else {
                    //denied
                    throw new AccessDeniedHttpException(trans("errors.permission_denied"));
                }
            } else {
                //Does not have to create this permission.
                return $next($request);
            }
        }
    }
}
