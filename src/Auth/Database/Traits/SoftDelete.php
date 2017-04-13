<?php
namespace Encore\Admin\Auth\Database\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Created by PhpStorm.
 * User: never615
 * Date: 13/04/2017
 * Time: 5:52 PM
 */
trait SoftDelete
{
    use SoftDeletes;

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
