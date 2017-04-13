<?php

namespace Encore\Admin\Auth\Database;


use Encore\Admin\Auth\Database\Traits\SoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Subject extends Model
{

    use SoftDelete;
    
    /**
     * 动态设定查询数据范围
     *
     * 项目拥有者和招商拥有查看全部业务数据的能力
     * 子主体只能查看自己拥有的数据
     *
     * @param $query
     */
    public function scopeDynamicData($query)
    {
        //1.获取当前登录账户属于哪一个主体
        $currentSubject = Auth::guard("admin")->user()->subject;
        //2.获取当前主体的所有子主体
        $ids = $currentSubject->getChildrenSubject($currentSubject->id);
        //3.限定查询范围为所有子主体
        $query->whereIn('id', $ids);
    }

    protected $fillable = [
        'id',
        'name'
    ];

    /**
     * 获取该主题下所有管理账号
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminUsers()
    {
        return $this->hasMany(Administrator::class);
    }
    
    public function reports(){
        return $this->hasMany(Report::class);
    }


    /**
     * 获得该主体拥有的全部权限
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, "subject_permissions", 'subject_id', 'permission_id');
    }


    /**
     * 获取所有子主体id,包括自身
     *
     * @return array
     */
    public function getChildrenSubject($subjectId)
    {
        $idResults = [$subjectId];

        $ids = static::where("parent_id", $subjectId)->pluck("id");
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $idResults = array_merge($idResults, $this->getChildrenSubject($id));
            }
        }

        return $idResults;
    }


}
