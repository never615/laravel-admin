<?php
namespace Encore\Admin\Grid\Exporters;

use Encore\Admin\Auth\Database\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;


/**
 * 辅助导出功能使用的工具
 *
 * Created by PhpStorm.
 * User: never615
 * Date: 29/03/2017
 * Time: 7:48 PM
 */
class ExportUtils
{
    /**
     * 移除一些总是无须导出的列
     *
     * @param array $array
     * @return array
     */
    public static function removeInvalids(array $array)
    {
        foreach ($array as $key => $value) {
            unset($array[$key]["images"]);
            unset($array[$key]["image"]);
            unset($array[$key]["icon"]);
            unset($array[$key]["logo"]);
        }

        return $array;
    }


    public static function removeInvalidsByCollection(Collection $datas)
    {
        $datas->forget(["images", "image", "icon", "logo"]);

        return $datas;
    }

    public static function dynamicData($tableName, $subjectId, $query)
    {
        if (Schema::hasColumn($tableName, 'subject_id')) {
            //1.获取当前登录账户属于哪一个主体
            $currentSubject = Subject::find($subjectId);
            //2.获取当前主体的所有子主体
            $ids = $currentSubject->getChildrenSubject($currentSubject->id);
            //3.限定查询范围为所有子主体
            $query->whereIn($tableName.'.subject_id', $ids);
        }

        return $query;
    }

    public static  function formatInput($tableName,$inputs)
    {
        foreach ($inputs as $key => $input) {
            if (strpos($key, "_") != 0) {
                $inputs[$tableName.".".$key] = $input;
                unset($inputs[$key]);
            }
        }
        return $inputs;
    }


}
