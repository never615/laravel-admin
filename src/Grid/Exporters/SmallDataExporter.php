<?php
namespace Encore\Admin\Grid\Exporters;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 小量数居导出
 * 
 * Created by PhpStorm.
 * User: never615
 * Date: 29/03/2017
 * Time: 8:35 PM
 */
abstract class SmallDataExporter extends \Encore\Admin\Grid\Exporters\AbstractExporter
{

    protected $callback;

    /**
     * {@inheritdoc}
     */
    public function export()
    {

        $filename = $this->getTable();

        $subjectId = Auth::user()->subject_id;

        $query = $this->getQuery($subjectId);
        $count = $query->count();
        if ($count < 20000) {
            Excel::create(mt_trans($filename), function ($excel) use ($query) {
                $excel->sheet('sheet1', function ($sheet) use ($query) {
                    $firstWrite = true;

                    $query->chunk(500, function ($data) use ($sheet, &$firstWrite) {
                        $data = $data->toArray();
                        $data = $this->customData($data) ? $this->customData($data) : $data;
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
            echo <<<EOT
<script type="text/javascript">
alert("该模块暂不支持大量数据导出");
window.close()
</script>
EOT;
        }
    }

    /**
     * 自定义数据处理
     *
     * @param $data
     * @return mixed
     */
    public abstract function customData($data);


}
