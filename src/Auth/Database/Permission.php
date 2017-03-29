<?php

namespace Encore\Admin\Auth\Database;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Permission extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.permissions_table'));

        parent::__construct($attributes);
    }

    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id');
    }

    /**
     * 查询对应权限的所有子权限
     *
     * @return Collection|static
     */
    public function subPermissions()
    {
        $tempPermissions = new Collection();
        $permissions = static::where("parent_id", $this->id)->get();
        $tempPermissions = $tempPermissions->merge($permissions);
        foreach ($permissions as $permission) {
            $tempPermissions = $tempPermissions->merge($permission->subPermissions());
        }

        return $tempPermissions;
    }

    /**
     * 获取该权限的所有长辈权限
     */
    public function elderPermissions()
    {
        $tempPermissions = new Collection();

        $permission = static::find($this->parent_id);
        if ($permission) {
            $tempPermissions = $tempPermissions->push($permission);
            $temp = $permission->elderPermissions();
            if ($temp->count() > 0) {
                $tempPermissions = $tempPermissions->merge($temp);
            }
        }

        return $tempPermissions;

    }


}
