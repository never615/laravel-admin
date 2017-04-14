<?php

namespace Encore\Admin\Grid\Exporters;

use App\Lib\TimeUtils;
use Encore\Admin\Auth\Database\Report;
use Encore\Admin\Facades\Admin;
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
 * Time: 8:35 PM.
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
        $fileName = mt_trans($tableName).'_'.$now.'_'.substr(time(), 5).'.csv';

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

        if ($count < 20001) {
            $response = new StreamedResponse(null, 200, [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ]);
            $response->setCallback(function () use ($query, $tableName) {
                $out = fopen('php://output', 'w');
                fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM

                $firstWrite = true;

                $query = $this->customQuery($query);

                $query->orderBy($tableName.'.id')->chunk(500, function ($data) use (&$firstWrite, $out) {
                    $data = json_decode(json_encode($data), true);

                    $data = $this->customData($data);
                    //有一些列总是不导出,如icon,image,images
                    $data = ExportUtils::removeInvalids($data);
                    //写列名
                    if ($firstWrite) {
                        $columnNames = [];
                        //获取列名
                        foreach ($data[0] as $key => $value) {
                            $columnNames[] = admin_translate($key, 'coupon');
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
            $tableName = mt_trans($tableName);
            if (Report::where('finish', false)->where('name', 'like', "$tableName%")->exists()) {
                $script = <<<'EOT'
layer.confirm('该模块有任务正在运行,请稍后再试.', {
  btn: ['确认'] //按钮
}, function(){
 window.close();
});

EOT;
                Admin::script($script);
            } else {
                $className = $this->exporterJob();
                if (empty($className)) {
                    $script = <<<'EOT'
layer.confirm('该模块暂不支持大量数据导出.', {
  btn: ['确认'] //按钮
}, function(){
 window.close();
});
EOT;
                    Admin::script($script);
                } else {
                    $report = Report::create([
                        'name'          => $fileName,
                        'status'        => Report::NOT_START,
                        'subject_id'    => $subjectId,
                        'admin_user_id' => Auth::user()->id,
                    ]);

                    $class = new ReflectionClass($className); // 建立 Person这个类的反射类
                    $instance = $class->newInstanceArgs([Input::all(), $subjectId, $report->id]); // 相当于实例化Person 类
                    dispatch($instance->onQueue('exporter'));

                    $script = <<<'EOT'
layer.confirm('数据量过大将在后台导出,点击确认进入报表管理查看.', {
  btn: ['确认','取消'] //按钮
}, function(){
  window.location.href='/admin/reports';
}, function(){
   window.close();
});
EOT;

                    Admin::script($script);
                }
            }
        }
    }

    /**
     * 自定义数据处理.
     *
     * @param $data
     *
     * @return mixed
     */
    abstract public function customData($data);

    abstract public function customQuery($query);

    /**
     * 需要大数据导出,则返回对应的job,不需要则不用重写此方法.
     *
     * @return null
     */
    abstract public function exporterJob();

    abstract public function model();

    abstract public function filter($filter);
}
