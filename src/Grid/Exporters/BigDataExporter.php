<?php
namespace Encore\Admin\Grid\Exporters;

use App\Lib\TimeUtils;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Grid\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use ReflectionClass;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Created by PhpStorm.
 * User: never615
 * Date: 29/03/2017
 * Time: 8:35 PM
 */
abstract class BigDataExporter extends \Encore\Admin\Grid\Exporters\AbstractExporter
{

    /**
     * {@inheritdoc}
     */
    public function export()
    {

        $tableName = $this->getTable();
        $now = TimeUtils::getNowTime();
        $fileName = mt_trans($tableName)."_".$now."_".substr(time(), 5);

        $subjectId = Auth::user()->subject_id;

        $inputs = Input::all();
        $class = new ReflectionClass($this->model()); // 建立 Person这个类的反射类
        $instance = $class->newInstance(); // 相当于实例化Person 类
        $model = new Model($instance);
        $filter = new Filter($model);
        $this->filter($filter);
        $inputs = ExportUtils::formatInput($tableName, $inputs);
        $query = $filter->executeForQuery($inputs, $subjectId, true);
        $query = ExportUtils::dynamicData($tableName, $subjectId, $query);

        $count = $query->count();

        if ($count < 20000) {
            $response = new StreamedResponse(null, 200, [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'.csv"',
            ]);
            $response->setCallback(function () use ($query, $tableName) {
                $out = fopen('php://output', 'w');
                fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM

                $firstWrite = true;

                $query = $this->customQuery($query);

                $query->orderBy($tableName.".id")->chunk(500, function ($data) use (&$firstWrite, $out) {

                    $data = json_decode(json_encode($data), true);

                    $data = $this->customData($data);
                    //有一些列总是不导出,如icon,image,images
                    $data = ExportUtils::removeInvalids($data);
                    //写列名
                    if ($firstWrite) {
                        $columnNames = [];
                        //获取列名
                        foreach ($data[0] as $key => $value) {
                            $columnNames[] = admin_translate($key, "coupon");
                        }
                        fputcsv($out, $columnNames);

                        unset($columnNames);
                        $firstWrite = false;
                    }
                    foreach ($data as $item) {
                        fputcsv($out, $item);
                    }
                });

                fclose($out);
            });
            $response->send();
            exit;
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
                $instance = $class->newInstanceArgs([Input::all(), $subjectId]); // 相当于实例化Person 类

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

    /**
     * 自定义数据处理
     *
     * @param $data
     * @return mixed
     */
    public abstract function customData($data);

    public abstract function customQuery($query);

    /**
     * 需要大数据导出,则返回对应的job,不需要则不用重写此方法
     *
     * @return null
     */
    public abstract function exporterJob();

    public abstract function model();

    public abstract function filter($filter);


}
