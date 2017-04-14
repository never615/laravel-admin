<?php

namespace Encore\Admin\Controllers;

use App\Exceptions\PermissionDeniedException;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Auth\Database\Subject;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin::lang.roles'));
            $content->description(trans('admin::lang.list'));
            $content->body($this->grid()->render());
        });
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
            $content->header(trans('admin::lang.roles'));
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
            $content->header(trans('admin::lang.roles'));
            $content->description(trans('admin::lang.create'));
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Role::class, function (Grid $grid) {
            $grid->model()->dynamicData();

            $grid->id('ID')->sortable();
            $grid->slug(trans('admin::lang.slug'));
            $grid->name(trans('admin::lang.name'));
            $grid->subject()->name('所属主体');

            $grid->created_at(trans('admin::lang.created_at'));
            $grid->updated_at(trans('admin::lang.updated_at'));

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                //                if ($actions->row->slug == 'administrator') {
//                    $actions->disableDelete();
//                }
                //不能删除自己的角色
                if (Admin::user()->isRole($actions->row->slug) && Admin::user()->subject_id == $actions->row->subject_id) {
                    $actions->disableDelete();
                }
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(Role::class, function (Form $form) {
            $form->display('id', 'ID');

            $form->select('subject_id', '主体')->options(function () {
                return Subject::dynamicData()->get()->pluck('name', 'id');
            })->rules('required');
            $form->text('slug', trans('admin::lang.slug'))->rules('required');
            $form->text('name', trans('admin::lang.name'))->rules('required');

            $form->multipleSelect('permissions', trans('admin::lang.permissions'))->options(function () {
                $subjectId = $this->subject_id;
                if ($subjectId == 1) {
                    $permissions = Permission::all();
                } else {
                    //主体拥有的权限需要加上那几个公共功能模块的权限

                    $permissions = new Collection();
                    $permissionsTemp = Subject::find($subjectId)->permissions;

                    $permissionsTemp = $permissionsTemp->merge(Permission::where('common', true)->get());

                    //查找子权限
                    foreach ($permissionsTemp as $permission) {
                        $permissions = $permissions->merge(Permission::where('slug', 'like',
                            "%$permission->slug%")->get());
                    }
                }

                return Permission::selectOptions($permissions->toArray(), false, false);
            });

//            $form->multipleSelect('permissions',
//                trans('admin::lang.permissions'))->options(Permission::all()->pluck('name', 'id'));

            $form->display('created_at', trans('admin::lang.created_at'));
            $form->display('updated_at', trans('admin::lang.updated_at'));

            $form->saving(function (Form $form) {
                if ($form->slug == config('admin.roles.owner')) {
                    throw new PermissionDeniedException('没有权限创建标识为owner的角色');
                }
            });
        });
    }
}
