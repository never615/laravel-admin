<?php

namespace Encore\Admin\Controllers\Base;

use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

abstract class AdminCommonController extends Controller
{
    use ModelForm, AdminOption;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header($this->getHeaderTitle());
            $content->description($this->getIndexDesc());
            $content->body($this->grid()->render());
        });
    }

    protected function getIndexDesc()
    {
        return trans('admin::lang.list');
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header($this->getHeaderTitle());
            $content->description(trans('admin::lang.edit'));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header($this->getHeaderTitle());
            $content->description(trans('admin::lang.create'));
            $content->body($this->form());
        });
    }

    protected function grid()
    {
        return Admin::grid($this->getModel(), function (Grid $grid) {
            $this->defaultGridOption($grid);
        });
    }

    protected function defaultGridOption($grid)
    {
        $grid->model()->dynamicData();
        $grid->model()->orderBy('id');

        $grid->id('ID')->sortable();

        $this->gridOption($grid);

//            $grid->created_at(trans('admin::lang.created_at'));
        $grid->updated_at(trans('admin::lang.updated_at'));

        $grid->filter(function ($filter) {
            // 禁用id查询框
            $filter->disableIdFilter();
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });
    }

    protected function form()
    {
        return Admin::form($this->getModel(), function (Form $form) {
            $this->defaultFormOption($form);
        });
    }

    protected function defaultFormOption($form)
    {
        $form->display('id', 'ID');
        $this->formOption($form);
        $form->display('created_at', trans('admin::lang.created_at'));
        $form->display('updated_at', trans('admin::lang.updated_at'));
    }
}
