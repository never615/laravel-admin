<?php

namespace Encore\Admin\Grid\Displayers;

use Encore\Admin\Admin;

class SwitchDisplay extends AbstractDisplayer
{
    protected $states = [
        'on'  => ['value' => 1, 'text' => 'ON', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => 'OFF', 'color' => 'default'],
    ];

    protected function updateStates($states)
    {
        foreach (array_dot($states) as $key => $state) {
            array_set($this->states, $key, $state);
        }
    }

    public function display($states = [])
    {
        $this->updateStates($states);

        $name = $this->column->getName();

        $class = "grid-switch-{$name}";

        $script = <<<EOT
        
        
var isError=false;

$('.$class').bootstrapSwitch({
    size:'mini',
    onText: '{$this->states['on']['text']}',
    offText: '{$this->states['off']['text']}',
    onColor: '{$this->states['on']['color']}',
    offColor: '{$this->states['off']['color']}',
    onSwitchChange: function(event, state){
    
        if(isError){
            isError=false;
            return;
        }

        $(this).val(state ? 'on' : 'off');
        var that=$(this);

        var pk = $(this).data('key');
        var value = $(this).val();

        $.ajax({
            url: "{$this->grid->resource()}/" + pk,
            type: "POST",
            data: {
                $name: value,
                _token: LA.token,
                _method: 'PUT'
            },
            success: function (data) {
                toastr.success(data.message);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                isError=true;
                var msg=""; 
                if (XMLHttpRequest.responseJSON && XMLHttpRequest.responseJSON.error) {
                    //后台有专门返回的错误信息的情况
                    msg += XMLHttpRequest.responseJSON.error;
                } else {
                    //错误不是后台专门返回的 422除外
                    if (XMLHttpRequest.status == 422) {
                        var erroMsg = JSON.parse(XMLHttpRequest.responseText);
                        $.each(erroMsg, function (k, v) {
                            msg += v[0] + "\\n";
                        });
                    } else {
                        //错误不是后台专门返回的 
                        msg += XMLHttpRequest.statusText + ":" + XMLHttpRequest.status;
                    }
                }
                //拿着msg做出提示
                notify.alert(3, msg, 3);
                console.log(state);
                that.bootstrapSwitch('toggleState');
//                location.reload();
            }
        });
    }
});

EOT;

        Admin::script($script);

        $key = $this->row->{$this->grid->getKeyName()};

        $checked = $this->states['on']['value'] == $this->value ? 'checked' : '';

        return <<<EOT
        <input type="checkbox" class="$class" $checked data-key="$key" />
EOT;
    }
}
