<?php

namespace Encore\Admin\Controllers;


use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Subject;
use Encore\Admin\Controllers\Base\AdminCommonController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class SubjectController extends AdminCommonController
{

    /**
     * 获取这个模块的标题
     *
     * @return mixed
     */
    protected function getHeaderTitle()
    {
        return "主体";
    }

    /**
     * 获取这个模块的Model
     *
     * @return mixed
     */
    protected function getModel()
    {
        return Subject::class;
    }

    protected function gridOption(Grid $grid)
    {
        $grid->name();
        $grid->parent_id("归属")->display(function ($parent_id) {
            $subject = Subject::find($parent_id);
            if ($subject) {
                return $subject->name;
            } else {
                return "项目拥有者";
            }
        });


        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if (Admin::user()->subject->id == $actions->row->id) {
                $actions->disableDelete();
            }

        });

    }

    protected function formOption(Form $form)
    {
        $form->text("name")->rules('required');
        $form->image("logo");
        $form->select("parent_id", "父级主体")->options(Subject::dynamicData()->get()->pluck("name", "id"));

        if (Admin::user()->isRole(config("admin.roles.owner"))) {
            $permissions = Permission::where("parent_id", 0)->get();
        } else {
            $permissions = Admin::user()->subject->permissions()->where("parent_id", 0)->get();
        }


//        $form->multipleSelect('permissions',
//            "可用功能")->options($permissions->pluck("name", "id"));

        $form->multipleSelect('permissions', "可用功能")->options(Permission::selectOptions($permissions->toArray(), false, false));

        $form->saving(function (Form $form) {
            if ($form->permissions && $form->model()->permissions != $form->permissions && Admin::user()->subject_id == $form->model()->id) {
                throw new AccessDeniedHttpException("无权修改主体拥有的功能,请联系上次管理.");
//                throw new PermissionDeniedException("无权修改主体拥有的功能,请联系上次管理.");
            }
        });

    }
}
