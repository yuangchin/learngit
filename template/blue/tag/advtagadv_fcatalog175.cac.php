<?php
defined('M_COM') || exit('No Permission');
$advtagadv_fcatalog175 = array (
  'ename' => 'adv_fcatalog175',
  'tclass' => 'advertising',
  'template' => '<div class="ad">{if $v[\'html\']}{html}{elseif $v[\'image\']}<a href="{link}" target="_blank">{c$image [cname=image/] [tclass=image/] [tname=image/] [val=u/] [maxwidth=300/] [maxheight=125/]}<img src="{url}" width="300" height="125" border="0" />{/c$image}</a>{elseif $v[\'flash\']}{c$flash [cname=flash/] [tclass=flash/] [tname=flash/] [val=u/] [width=300/] [height=125/]}{playbox}{if $v[\'link\']}<a class="ad-link" href="{$v[\'link\']}" style="margin-top:-{height}px;width:{width}px;height:{height}px;" target="_blank"></a>
            {/if}{/c$flash}{/if}</div>',
  'setting' => 
  array (
    'limits' => 1,
    'casource' => 'fcatalog175',
    'validperiod' => '1',
    'orderstr' => ' a.vieworder DESC ',
  ),
) ;