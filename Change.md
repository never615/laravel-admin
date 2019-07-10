#### 自动翻译
使用时表格/表单/过滤器上的字段名,不设置label的情况下,会自动从翻译(详见admin_translate()).
* 修改helpers文件,增加的`admin_translate()`和`admin_translate_arr()`方法
* 修改`\Encore\Admin\Form\Field`的`formatLabel()`,`setForm()`,`__construct`.
* 修改`Encore\Admin\Grid`中的`__call()`
* 修改`Encore\Admin\Grid\Filter\AbstractFilter`中的`formatLabel() __construct() setTable`
* 修改`Encore\Admin\Grid\Filter`中的`__call()`
* 修改`src/Form/Field/DateRange.php`的构造函数和setForm()
* 修改`src/Form/Field/Embeds.php`的构造函数和setForm()
* 修改`src/Form/Field/HasMany.php`的构造函数和setForm()

#### 表单创建是隐藏字段方法
使用上通过`$form->hideFieldsByCreate([]);`
用于创建时排除一些字段在表单创建是不出现
* 修改`\Encore\Admin\Form\Builder`,增加`hideFieldsByCreate()`.
* 修改`Encore\Admin\Form`,增加`hideFieldsByCreate()`方法.
 

#### 修改授权验证中间件,如果是json请求,以json方式响应

#### 默认导出处理者可以通过config自定义
* 修改`src/Grid/Exporter.php`
```
    /*
    * set default Exporter
    */
    'exporter'=>Encore\Admin\Grid\Exporters\CsvExporter::class,
```

#### Field增加`addElementClass2()`方法
用该方法添加class不会覆盖原Field自动生成的class

#### 授权中间件,根据请求的Accept做不同响应
`Encore\Admin\Middleware\Authenticate`:
```
  if ($request->expectsJson()) {
                return response()
                    ->json(['error' => "未授权,请登录"], 401);
            } else {
                return redirect()->guest($redirectTo);
            }

```

#### 处理部分form的view文件,添加class以便于使用js代码控制整体隐藏和显示
* `resources/views/form/display.blade.php`添加了`{{$class}}`,
*  form->hasMany 的view进行了一次div包裹,以便于使用js代码控制整体隐藏和显示


#### 库(tree)里面用到关键字path了,model如果也有path命名的列就会有问题.修改path为resource_path
涉及文件有:
* `resources/views/tree.blade.php`
* `resources/views/tree/branch.blade.php`
* `src/Tree.php`

#### 将管理端报错计入lavavel.log
`src/Exception/Handler.php`添加:
管理端的异常处理者,抛出正常抛出的异常,通过文字描述大于30,因为程序异常的话,是大量的堆栈信息.
记录到laravel.log便于查看和排查问题.(主要也是在laravel.log记录的error级别的异常报警,便于线上发生问题及时修复)
```
  if (!($exception instanceof ResourceException)) {
            if (strlen($exception->getMessage()) > 25) {
                \Log::error("管理端错误");
            }
            \Log::warning($exception);
        }
```

#### form->destory失败时的异常处理优化
修改文件:`Encore\Admin\Form`中的`destroy()`方法
不返回异常退栈信息,提示存在关联数据无法删除,然后打印错误到日志.


#### 修改`Encore\Admin\Form\Tools`,增加设置翻译内容的方法(`setTrans()`),主要用户调整删除弹框提示语.

#### 导出支持swoole
修改`Grid`