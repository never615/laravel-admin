<?php
namespace Encore\Admin\Grid\Exporters;

use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use ReflectionClass;

/**
 * Created by PhpStorm.
 * User: never615
 * Date: 29/03/2017
 * Time: 8:35 PM
 */
abstract class BigDataExporter extends \Encore\Admin\Grid\Exporters\AbstractExporter
{

    protected $callback;

    /**
     * {@inheritdoc}
     */
    public function export()
    {

//        $inputs=Input::all();
//        Log::info($inputs);

        $filename = $this->getTable();
        $query = $this->getQuery();
        $count = $query->count();
        if ($count < 2) {
            Excel::create(mt_trans($filename), function ($excel) use ($query) {
                $excel->sheet('sheet1', function ($sheet) use ($query) {
                    $firstWrite = true;

                    $query->chunk(500, function ($data) use ($sheet, &$firstWrite) {
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
        } else {
            $className = $this->exporterJob();
            if (empty($className)) {
                echo <<<EOT
<script type="text/javascript">
alert("该模块暂不支持大量数据导出");
window.close()
</script>
EOT;
            } else {
                $class = new ReflectionClass($className); // 建立 Person这个类的反射类
                $instance = $class->newInstanceArgs([$filename, Input::all()]); // 相当于实例化Person 类

                dispatch($instance->onQueue('exporter'));

                echo <<<EOT
<script type="text/javascript">
alert("导出数据量过大,将会进行后台导出,导出进度请到报表管理查看");
window.close()
</script>
EOT;
            }
        }
    }

    public abstract function customData($data);

    public function exporterJob()
    {
        return null;
    }


}
