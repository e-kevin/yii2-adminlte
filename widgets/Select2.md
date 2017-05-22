# Select2 Widget

用法
---
> 如果Select2是在Modal模态窗或其他弹出窗口内运行的话，则需要在页面的Asset内添加依赖
`wonail\adminlte\assetBundle\Select2Asset`，否则会导致select2样式无法正确加载。
除此情况外，可直接调用`\wonail\adminlte\widgets\Select2`

```php
<?php
   $data = [
     'Alaska',
     'California',
     'Delaware',
     'Tennessee',
     'Texas',
     'Washington',
  ];
  
  echo \wonail\adminlte\widgets\Select2::widget([
      'name' => 'state',
      'value' => 0, // 默认选中值
      'data' => $data, // 下拉选框数据
      'options' => [
        'multiple' => true,
        'placeholder' => '请选择',
      ],
      'pluginOptions' => [
        'allowClear' => true
      ],
  ]);
?>
```
使用方法请查看[官网示例](https://select2.github.io/examples.html)
