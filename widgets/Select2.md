# Select2 Widget

用法
---

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
