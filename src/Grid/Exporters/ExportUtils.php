<?php
namespace Encore\Admin\Grid\Exporters;


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
    
    

}
