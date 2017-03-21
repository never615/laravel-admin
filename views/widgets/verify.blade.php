<div class="container-fluid col-lg-10 col-lg-offset-1">
    <div class="panel panel-default ">
        <div class="panel-heading">
            <h3 class="panel-title">{{$title}}</h3>
        </div>
        <div class="panel-body">
            <div class="panel-body col-lg-10 col-lg-offset-1">
                <div class="row ">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control verify-num" placeholder="{{$placeholder}}">
                         <span class="input-group-btn">
                                <button class="btn btn-default verify-search" type="button">搜索</button>
                                <button class="btn btn-danger verify-clear" type="button">清除</button>
                                <button class="btn btn-success verify-submit" type="button">{{$submit}}</button>
                         </span>
                    </div><!-- /input-group -->
                </div><!-- /.row -->
            </div>
        </div>

        <table class="table  table-striped table-bordered">
            <tbody class="verify-result">
            </tbody>
        </table>
    </div>
</div>

<script>
    $(".verify-num").on("keyup", function (e) {
        if (e.keyCode != 8 && e.keyCode != 46) {
            this.value = this.value.replace(/[^\d]/g, '');
            var value = $(this).val();

            if (value.length == 11) {
                //长度为11,按手机号算,3 4 4分
                this.value = value.replace(/(^.{3})(.{4})(.{4})/g, "$1 $2 $3");
            } else {
                this.value = value.replace(/(.{4})/g, "$1 ");
            }

            this.value = trimAll(this.value);
        }
    });

    $(".verify-num").on("keydown", function (e) {
        if (e.keyCode == 13) {
            requestInfo();
        } else {
            $(".verify-result").empty();
        }
    });


    var clear = function () {
        $(".verify-num").val("");
        $(".verify-result").empty();
    };


    var requestInfo = function () {
        //拿到输入的卡号
        var verifyNum = trimAll($(".verify-num").val(), true);
        doAjax("{{$url}}/" + verifyNum, "GET", "", function (data) {
            $('.verify-result').empty();
            for (var key in data) {
                if (data.hasOwnProperty(key)) { //filter,只输出man的私有属性
                    $(".verify-result").append('<tr> <td>' + key + '</td> <td>' + (data[key] ? data[key] : "") + '</td> </tr>');
                }
            }
        });
    };

    $(".verify-search").on("click", function () {
        console.log("search");
        requestInfo();

    });

    $(".verify-clear").on("click", function () {
        clear();
    });

    $(".verify-submit").on("click", function () {
        var verifyNum = trimAll($(".verify-num").val(), true);
        doAjax("{{$url}}/" + verifyNum, "PUT", "", function (data) {
            requestInfo();
            notify.alert(1, "成功", 4);
        });
    });

</script>

