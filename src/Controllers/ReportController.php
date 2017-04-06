<?php

namespace Encore\Admin\Controllers;


use Encore\Admin\Auth\Database\Report;
use Encore\Admin\Controllers\Base\AdminCommonController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;


class ReportController extends AdminCommonController
{

    /**
     * 获取这个模块的标题
     *
     * @return mixed
     */
    protected function getHeaderTitle()
    {
        return "大数据报表";
    }

    protected function getIndexDesc(){
        return "管理";
    }

    /**
     * 获取这个模块的Model
     *
     * @return mixed
     */
    protected function getModel()
    {
        return Report::class;
    }

    protected function gridOption(Grid $grid)
    {

        $grid->disableActions();
        $grid->disableCreation();
        $grid->disableExport();
        $grid->name();
        $grid->finish();
        $grid->status();
        $grid->column("download")->display(function () {
            $url=Storage::disk("public")->url("exports/".$this->name);
            return <<<EOT
                <a href="$url" target="_blank">点击下载</a>
EOT;
        });
        $grid->subject()->name("主体");
        $grid->adminUser()->name("创建人");
        $grid->desc();
        $grid->created_at();


    }


    protected function formOption(Form $form)
    {
        // TODO: Implement formOption() method.
    }
}
