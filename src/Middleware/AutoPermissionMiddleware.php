<?php
/**
 * Created by PhpStorm.
 * User: never615
 * Date: 10/03/2017
 * Time: 8:36 PM.
 *
 * You need set permission's slug by routeName or url( auth/roles of https://xxx.com/admin/auth/roles )
 */

namespace Encore\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class AutoPermissionMiddleware.
 *
 * 参考文档:https://github.com/never615/laravel-admin/wiki/%E9%A1%B9%E7%9B%AE%E8%AE%BE%E8%AE%A1#关于自动校验权限
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
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currentRouteName = Route::currentRouteName();
        $routenameArr = explode('.', $currentRouteName);

        if (count($routenameArr) == 2) {
            $subRouteName = $routenameArr[1];

            if ($subRouteName == 'edit') {
                $currentRouteName = $routenameArr[0].'.index';
            }

            if ($subRouteName == 'store' || $subRouteName == 'update') {
                $currentRouteName = $routenameArr[0].'.create';
            }
        }

        //权限管理有该权限,检查用户是否有该权限
        if (Auth::guard('admin')->user()->can($currentRouteName)) {
            //pass
            return $next($request);
        } else {
            if (Auth::guard('admin')->user()->can($routenameArr[0])) {
                //pass
                return $next($request);
            } else {
                //不拥有或者不存在对应权限的路由不能访问,控制面板除外
                //denied
                throw new AccessDeniedHttpException(trans('errors.permission_denied'));
            }
        }

//        if (Auth::guard("admin")->user()->isOwner()) {
//            //pass
//            return $next($request);
//        } else {
//            //1.检查当前路由在权限列表中有没有对应的权限,如果没有设置该功能对应的权限,则默认通过
////            if (count($routenameArr) == 2) {
////                $subRouteName = $routenameArr[1];
////
////                if ($subRouteName == "edit") {
////                    $currentRouteName = $routenameArr[0].".index";
////                }
////
////                if ($subRouteName == "store" || $subRouteName == "update") {
////                    $currentRouteName = $routenameArr[0].".create";
////                }
////            }
//            $permission = Permission::where('slug', $currentRouteName)->first();
//            if ($permission) {
//                //权限管理有该权限,检查用户是否有该权限
//                if (Auth::guard("admin")->user()->can($permission->slug)) {
//                    //pass
//                    return $next($request);
//                } else {
//                    //denied
//                    throw new AccessDeniedHttpException(trans("errors.permission_denied"));
//                }
//            } else {
//                //Does not have to create this permission.
//                return $next($request);
//            }
//        }
    }
}
