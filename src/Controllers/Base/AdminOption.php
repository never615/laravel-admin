<?php

namespace Encore\Admin\Controllers\Base;

use Encore\Admin\Form;
use Encore\Admin\Grid;

/**
 * Created by PhpStorm.
 * User: never615
 * Date: 08/03/2017
 * Time: 3:05 PM.
 */
trait AdminOption
{
    /**
     * 获取这个模块的标题.
     *
     * @return mixed
     */
    abstract protected function getHeaderTitle();

    /**
     * 获取这个模块的Model.
     *
     * @return mixed
     */
    abstract protected function getModel();

    abstract protected function gridOption(Grid $grid);

    abstract protected function formOption(Form $form);
}
