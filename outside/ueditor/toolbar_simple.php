<?php
/**
 * �ٶȱ༭�����װ汾�İ�ť����
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
return <<<JS
    toolbars: [
        [
            'undo', //����
            'redo', //����
            'bold', //�Ӵ�
            'italic', //б��
            'underline', //�»���
            'strikethrough', //ɾ����
            'subscript', //�±�
            'superscript', //�ϱ�
            'fontborder', //�ַ��߿�
            'forecolor', //������ɫ
            'fontfamily', //����
            'fontsize', //�ֺ�
            'justifyleft', //�������
            'justifyright', //���Ҷ���
            'justifycenter', //���ж���
            'justifyjustify', //���˶���
            'selectall', //ȫѡ
            'emotion', //����
            'drafts' // �Ӳݸ������
        ]
    ],
JS;
