<?php
namespace Encore\Admin\Grid\Exporters;

use Encore\Admin\Grid\Exporters\ExportUtils;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Created by PhpStorm.
 * User: never615
 * Date: 29/03/2017
 * Time: 8:35 PM
 */
abstract class ExcelExporter extends \Encore\Admin\Grid\Exporters\AbstractExporter
{
    /**
     * {@inheritdoc}
     */
    public function export()
    {
//        ini_set('max_execution_time', 0);

        $filename = $this->getTable();
        $query = $this->getQuery();

        Excel::create($filename, function ($excel) use ($query) {
            $excel->sheet('sheet1', function ($sheet) use ($query) {
                $firstWrite = true;

                $query->chunk(200, function ($data) use ($sheet, &$firstWrite) {
                    $data = $data->toArray();
                    $data = $this->customData($data);
                    //有一些列总是不导出,如icon,image,images
                    $data = ExportUtils::removeInvalids($data);

                    if ($firstWrite) {
                        $columnNames = [];
                        //获取列名
                        foreach ($data[0] as $key => $value) {
                            $columnNames[] = admin_translate($key, "coupons");
                        }
                        $sheet->appendRow($columnNames);
                        $sheet->rows($data);

                        $firstWrite = false;
                    } else {
                        $sheet->rows($data);
                    }
                });
            });
        })->export('xls');

        exit;
    }


    public abstract function customData($data);
}
