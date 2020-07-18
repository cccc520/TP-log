<?php
/**
 * ============================================================================
 * 版权所有 2017-2077 tpframe工作室，并保留所有权利。
 * @link http://www.tpframe.com/
 * @copyright Copyright (c) 2017 TPFrame Software LLC
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */
namespace app\backend\validate;
use app\common\validate\ValidateBase;
class SmsTemplete extends ValidateBase
{
    // 验证规则
    protected $rule = [
        'subject'              	=> 'require',
        'send_scene'             	      => 'require',
        'tpl_content'				   => 'require'
    ];

    // 验证提示
    protected $message = [
        'subject.require'               => '邮件主题不能为空',
        'send_scene.require'            => '发送场景不能为空',
        'tpl_content.require'           => '模块内容不能为空',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['subject','send_scene','tpl_content'],
        'edit'  =>  ['subject','send_scene','tpl_content']
    ];
}