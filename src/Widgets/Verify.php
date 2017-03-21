<?php

namespace Encore\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Verify extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.verify';

    /**
     * @var string
     */
    protected $title = 'title';

    /**
     * 搜索框提示
     *
     * @var string
     */
    protected $placeholder = 'placeholder';

    /**
     * 提交按钮名字
     *
     * @var string
     */
    protected $submit = 'submit';

    /**
     * @var string
     */
    protected $content = 'content';

    /**
     * @var array
     */
    protected $tools = [];

    /**
     * 地址
     *
     * @var
     */
    protected $url;

    /**
     * Box constructor.
     *
     * @param string $title
     * @param string $placeholder
     * @param string $submit
     */
    public function __construct($title = '', $placeholder = "", $submit = "")
    {
        if ($title) {
            $this->title($title);
        }

        if ($placeholder) {
            $this->placeholder($placeholder);
        }

        if ($submit) {
            $this->submit($submit);
        }
    }


    /**
     * Set box title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function submit($submit)
    {
        $this->submit = $submit;

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }


    /**
     * Variables in view.
     *
     * @return array
     */
    protected function variables()
    {
        return [
            'title'       => $this->title,
            'placeholder' => $this->placeholder,
            'submit'      => $this->submit,
            'url'         => $this->url,
        ];
    }


    /**
     * Render box.
     *
     * @return string
     */
    public function render()
    {
        return view($this->view, $this->variables())->render();
    }


}
