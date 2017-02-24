<?php
$aguide = '<li>模板中的代码请遵循PHP函数定义的模式，即此段代码相当于一个PHP函数中的代码，并且必须使用return返回值，不能使用echo或print来产生结果值。
<li>代码中允许使用函数，函数可以是PHP内置函数、部分CMS系统定义函数或在系统模板目录中的function/utags.fun.php 中定义的函数。
<li>代码中允许内嵌当前可用的原始信息标识{$xxx}或特殊字段标识{u$xxx}，在执行代码之前系统会先行解析出内嵌标识的值。
<li>如果内嵌标识值为字串，以 \'{$xxx}\' 的格式来引用，如为数字，使用 intval(\'{$xxx}\') 或floatval(\'{$xxx}\')。';
?>