<?php
/**
 * 处理管理端左侧菜单
 *
 * 根据当前的登录用户拥有的权限动态的显示相应的功能
 *
 * 如果是项目的拥有者直接显示所有菜单
 *
 * Created by PhpStorm.
 * User: never615
 * Date: 11/11/2016
 * Time: 8:16 PM
 */
namespace Encore\Admin\Middleware;


use Closure;
use Encore\Admin\Auth\Database\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Sidebar
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
        //view()->share('comData',$this->getMenu());
//        $request->attributes->set('comData_menu', $this->getMenu());
        $request->attributes->set('sidebar', $this->getSidebar());
        return $next($request);
    }

    /**
     * 获取左边菜单栏
     * @return array
     */
    private function getSidebar()
    {
        $menus = [];
        if (Auth::user()->hasRole(config('auth.roles.owner'))) {
            //如果是项目的拥有者,直接返回所有的菜单
            
            
            $permissions = Permission::where('name', 'like', '%index')->get();
            $menus = $this->generateMenu($permissions, $menus);
            return $menus;
        } else {
            //查询当前登录用户所拥有的权限
            $roles = Auth::user()->roles;

            foreach ($roles as $role) {
                $permissions = $role->permissions()->where('name', 'like', '%index')->get();

                $menus = $this->generateMenu($permissions, $menus);
            }
            return $menus;
        }
    }

    /**
     * @param $permissions
     * @param $menus
     * @return mixed
     */
    private function generateMenu($permissions, $menus)
    {
        foreach ($permissions as $permission) {
            if (!array_key_exists($permission->group, $menus)) {
                $menus[$permission->group] = [];
            }
            array_push($menus[$permission->group], [
                'group_icon' => $permission->group_icon,
                'title' => $permission->display_name,
                'routeName' => $permission->name
            ]);

        }
        return $menus;
    }

}
