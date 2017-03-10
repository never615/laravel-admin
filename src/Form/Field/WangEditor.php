<?php
/**
 * Created by PhpStorm.
 * User: never615
 * Date: 09/03/2017
 * Time: 6:58 PM
 */
namespace Encore\Admin\Form\Field;
use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin::form.editor';

    protected static $css = [
        '/packages/wangEditor-2.1.23/dist/css/wangEditor.min.css',
    ];

    protected static $js = [
        '/packages/wangEditor-2.1.23/dist/js/wangEditor.min.js',
    ];

    public function render()
    {
        $this->script = <<<EOT

var editor = new wangEditor('{$this->id}');
    editor.create();

EOT;
        return parent::render();

    }
}