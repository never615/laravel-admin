<?php

namespace Encore\Admin\Jobs;


use App\Lib\TimeUtils;
use Encore\Admin\Grid\Exporters\ExportUtils;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Grid\Model;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mallto\Mall\Data\AdminUser;
use Mallto\Mall\Data\Coupon;
use Mallto\Mall\SelectConstants;
use ReflectionClass;

abstract class ExporterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var
     */
    protected $subjectId;

    protected $inputs;

    protected $table;


    /**
     * Create a new job instance.
     *
     * @param $inputs
     */
    public function __construct($inputs, $subjectId)
    {
        $this->inputs = $inputs;
        $this->subjectId = $subjectId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("执行导出任务");

//        Log::info($this->inputs);

        $expoter = $this->expoter();
        $className = $expoter->model();
        $class = new ReflectionClass($className); // 建立 Person这个类的反射类
        $instance = $class->newInstance(); // 相当于实例化Person 类
        $tableName = $instance->getTable();
        $this->table = $tableName;
        $model = new Model($instance);
        $filter = new Filter($model);
        $expoter->filter($filter);
        $this->inputs = ExportUtils::formatInput($tableName, $this->inputs);
        $query = $filter->executeForQuery($this->inputs, $this->subjectId, true);
        $query = ExportUtils::dynamicData($tableName, $this->subjectId, $query);
        $now = TimeUtils::getNowTime();
        $fp = fopen(storage_path('exports')."/".mt_trans($tableName)."_".$now."_".substr(time(), 5).".csv", "a");
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
        $firstWrite = true;
        $query = $expoter->customQuery($query);
        $query->orderBy($tableName.".id")->chunk(1000, function ($data) use (&$firstWrite, $fp, $expoter) {

            $data = json_decode(json_encode($data), true);

            $data = $expoter->customData($data);
            //有一些列总是不导出,如icon,image,images
            $data = ExportUtils::removeInvalids($data);
            //写列名
            if ($firstWrite) {
                $columnNames = [];
                //获取列名
                foreach ($data[0] as $key => $value) {
                    $columnNames[] = admin_translate($key, "coupon");
                }
                fputcsv($fp, $columnNames);

                unset($columnNames);
                $firstWrite = false;
            }
            foreach ($data as $item) {
                fputcsv($fp, $item);
            }
        });

        fclose($fp);

        Log::info("导出完成");
    }

    /**
     * The job failed to process.
     *
     * @param Exception $e
     */
    public function failed(Exception $e)
    {
        // 发送失败通知, etc...

        Log::info("导出失败");
        Log::info($e);
    }

    protected abstract function expoter();


}
