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
namespace app\backend\controller;
use \tpfcore\Core;
use \tpfcore\helpers\FileHelper;
use \think\Config;

class Spam extends AdminBase
{

    /*
        简单的评论过滤功能
    */
    public function index(){
    	IS_POST && $this->jump(Core::loadModel($this->name)->editSetting($this->param));
    	return $this->fetch(":spam/index",[
            'blacklist'=>FileHelper::get_dir(config("blacklist"))
    	]);
    }

} 
