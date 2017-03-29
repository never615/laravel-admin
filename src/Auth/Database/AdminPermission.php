<?php

namespace Encore\Admin\Auth\Database;

use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Log;

trait AdminPermission
{
    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if ($avatar) {
            return rtrim(config('admin.upload.host'), '/').'/'.trim($avatar, '/');
        }

        return asset('/packages/admin/AdminLTE/dist/img/user2-160x160.jpg');
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }

    /**
     * Check if user has permission.
     *
     * @param $permissionSlug
     *
     * @return bool
     */
    public function can($permissionSlug)
    {
        //1.项目拥有者拥有全部权限
        if ($this->isOwner()) {
            return true;
        }

        //2.用户拥有该权限通过
        if (method_exists($this, 'permissions')) {
            if ($this->permissions()->where('slug', $permissionSlug)->exists()) {
                return true;
            }
        }
        //3.用户拥有该权限的父权限,通过
        //先查询该权限的父权限,因为权限支持多级,所以要查询出该权限的所有长辈权限
        $permission = Permission::where('slug', $permissionSlug)->first();
        $elderPermissions = $permission->elderPermissions();
        //检查用户的权限中有没有$elderPermissions中的权限
        if ($elderPermissions && $this->permissions()->whereIn("id", $elderPermissions->pluck("id"))->exists()) {
            return true;
        }

        //4.用户的角色拥有该权限通过
        //5.用户的角色拥有该权限的父权限,通过
        foreach ($this->roles as $role) {
            if ($role->can($permissionSlug)) {
                return true;
            }

            Log::info($elderPermissions);
            Log::info($role->permissions);
            Log::info($role->permissions->pluck("id"));
            Log::info($elderPermissions->toArray());
            Log::info(array_pluck($elderPermissions->toArray(),"id"));
            Log::info($elderPermissions->pluck("id"));


            if ($elderPermissions && $role->permissions()->whereIn("id", $elderPermissions->pluck("id"))->exists()) {
                return true;
            }

        }


        return false;
    }

    /**
     * Check if user has no permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function cannot($permission)
    {
        return !$this->can($permission);
    }

    /**
     * Check if user is administrator.
     *
     * @return mixed
     */
    public function isAdministrator()
    {
        return $this->isRole(config("admin.roles.admin"));
    }

    public function isOwner()
    {
        return $this->isRole(config("admin.roles.owner"));
    }


    /**
     * Check if user is $role.
     *
     * @param string $role
     *
     * @return mixed
     */
    public function isRole($role)
    {
        return $this->roles()->where("subject_id", Admin::user()->subject->id)->where('slug', $role)->exists();
    }

    /**
     * Check if user in $roles.
     *
     * @param array $roles
     *
     * @return mixed
     */
    public function inRoles($roles = [])
    {
        return $this->roles()->where("subject_id", Admin::user()->subject->id)->whereIn('slug',
            (array) $roles)->exists();
    }

    /**
     * If visible for roles.
     *
     * @param $roles
     *
     * @return bool
     */
    public function visible($roles)
    {
        if (empty($roles)) {
            return true;
        }

        $roles = array_column($roles, 'slug');

        if ($this->inRoles($roles) || $this->isAdministrator()) {
            return true;
        }

        return false;
    }

    /**
     * 返回用户所有的权限
     * 包括角色包含的和单独权限拥有的
     */
    public function allPermissions()
    {
        $roles = $this->roles;
        $permissions = $this->permissions;
        foreach ($roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions;
    }
}
