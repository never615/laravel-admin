### 自动翻译
使用时表格/表单/过滤器上的字段名,不设置label的情况下,会自动从翻译(详见admin_translate()).
* 修改helpers文件,增加的`admin_translate()`和`admin_translate_arr()`方法
* 修改`\Encore\Admin\Form\Field`的`formatLabel()`,`setForm()`,`__construct`.
* 修改`Encore\Admin\Grid`中的`__call()`
* 修改`Encore\Admin\Grid\Filter\AbstractFilter`中的`formatLabel()`

### 表单创建是隐藏字段方法
使用上通过`$form->hideFieldsByCreate([]);`
用于创建时排除一些字段在表单创建是不出现
* 修改`\Encore\Admin\Form\Builder`,增加`hideFieldsByCreate()`.
* 修改`Encore\Admin\Form`,增加`hideFieldsByCreate()`方法.
 
### editable默认的emptytext
* 修改`Encore\Admin\Admin\Editable`中的`$option`

### 默认导出处理者可以通过config自定义

### 调用Field的addElementClass方式的时候,不会覆盖原Field自动生成的class
