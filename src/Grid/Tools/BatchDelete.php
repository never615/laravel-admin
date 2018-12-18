<?php

namespace Encore\Admin\Grid\Tools;

class BatchDelete extends BatchAction
{
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Script of batch delete action.
     */
    public function script()
    {
        $deleteConfirm = trans('admin.delete_confirm');
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');

        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    var id = {$this->grid->getSelectedRowsName()}().join();
    
    swal({
        title: "$deleteConfirm",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "$confirm",
        showLoaderOnConfirm: true,
        cancelButtonText: "$cancel",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '{$this->resource}/' + id,
                    data: {
                        _method:'delete',
                        _token:'{$this->getToken()}'
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        resolve(data);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        var msg = '';
                        if (XMLHttpRequest && XMLHttpRequest.responseText) { //ajax error, errors = xhr object
                            if (XMLHttpRequest.responseJSON && XMLHttpRequest.responseJSON.error) {
                                msg += XMLHttpRequest.responseJSON.error;
                            } else {
                                msg += XMLHttpRequest.status + ":" + XMLHttpRequest.statusText + ":" + XMLHttpRequest.responseText;
                            }
                        } else { 
                                msg += XMLHttpRequest.status;
                        }
                        
                        resolve({error:msg});
                    }                    
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if(data.error){
            swal(data.error, '', 'error');
        }else{
            if (typeof data === 'object') {
                if (data.status) {
                    swal(data.message, '', 'success');
                } else {
                    swal(data.message, '', 'error');
                }
            }
        }
    });
});

EOT;
    }
}
