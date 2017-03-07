<?php

namespace Encore\Admin\Auth\Database;

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
            return rtrim(config('admin.upload.host'), '/') . '/' . trim($avatar, '/');
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
     * 返回所有权限slug以index结尾的
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getIndexPermissions()
    {
        return $this->permissions()->where('slug', 'like', '%index')->get();
    }

    /**
     * Check if user has permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function can($permission)
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if (method_exists($this, 'permissions')) {
            if ($this->permissions()->where('slug', $permission)->exists()) {
                return true;
            }
        }

        foreach ($this->roles as $role) {
            if ($role->can($permission)) {
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
     * Check if user is owner.
     *
     * @return mixed
     */
    public function isOwner()
    {
        return $this->isRole(config("admin.roles.owner"));
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

    /**
     * Check if user is $role.
     *
     * @param string $role
     *
     * @return mixed
     */
    public function isRole($role)
    {
        return $this->roles()->where('slug', $role)->exists();
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
        return $this->roles()->whereIn('slug', (array)$roles)->exists();
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

        if ($this->inRoles($roles) || $this->isOwner()) {
            return true;
        }

        return false;
    }

    /**
     * 返回用户所有的权限,slug是以index结尾的
     * 包括角色包含的和单独权限拥有的
     *
     */
    public function allIndexPermissionArr()
    {
        $roles = $this->roles;
        $indexPermissions = $this->getIndexPermissions()->toArray();
        foreach ($roles as $role) {
            $arr=$role->getIndexPermissions()->toArray();
            $indexPermissions=array_merge($indexPermissions,$arr);
//            $indexPermissions->merge($arr);
//            $permissionsTemp = $role->indexPermissions();
//            $indexPermissions = array_merge($indexPermissions, $permissionsTemp);
        }

        return $indexPermissions;
    }
}
