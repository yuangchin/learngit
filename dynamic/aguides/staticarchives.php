<?php
$aguide = '<li>1.〖主动生成静态〗是管理员主动直接生成前台静态页面; 当网站建设完成，开始正常运营前建议使用一次。一般情况下不建议使用此操作</li>
<li>2.〖被动生成静态〗是当用户点击前台页面时，系统自动生成一个该访问静态页面（第一次是动态的，当第二次再点击这个页面时就是静态页面了，以后就会按规则设定的时间自动对已有的静态进行更新;），在网站正常运营期建议使用此设置，系统会自动生成静态页，避免大批量生成静态页面时对服务器资源的消耗</li>
<li>3.〖修复静态链接(补缺)〗给[被动生成静态] 生成静态预设静态规则的静态地址，对已先前已生成的静态地址将不在生成，防止以下情况前台生成静态时会出现页面不存在现象。 <br />
&nbsp;&nbsp;① 在未启动静态时添加的文章，之后再启动静态<br />
&nbsp;&nbsp;②静态文件被手动删除</li>
<li>4.〖修复静态链接(重写)&#160;&#160;〗：在以述3的情况下，强制生成所有的预设静态规则的静态地址，发下情况必须用此项</li>
<li>5.〖加入进程〗：与"立即执行"是静态操作的两种模式，通过加入进程并启动进程，利于长时间的无人值守操作。</li>';
?>