<?php
// +----------------------------------------------------------------------
// | Author: yaoyihong <510974211@qq.com>
// | Site home: http://www.tpframe.com
// +----------------------------------------------------------------------

namespace app\common\controller;

use think\Controller;
use tpfcore\helpers\Json;
use tpfcore\Core;
use think\Request;
/**
 * 系统通用控制器基类
 */
class ControllerBase extends Controller
{
    // 当前类名称
    protected $class;
    // 当前控制器名称
    protected $name;
    // 请求参数
    protected $param;
    // 请求的POST参数
    protected $post;
    
    /**
     * 基类初始化
     */
    protected function _initialize()
    {
        // 初始化请求信息
        $this->initRequestInfo();

        // 当前类名
        $this->class = get_called_class();

        if (empty($this->name)) {
            // 当前模型名
            $name       = str_replace('\\', '/', $this->class);
            $this->name = basename($name);
            if (\think\Config::get('class_suffix')) {
                $suffix     = basename(dirname($name));
                $this->name = substr($this->name, 0, -strlen($suffix));
            }
        }
    }
    
    /**
     * 初始化请求信息
     */
    final private function initRequestInfo()
    {
        $this->request->filter("tpfcore\helpers\StringHelper::paramFilter");
        defined('IS_POST')          or define('IS_POST',         $this->request->isPost());
        defined('IS_GET')           or define('IS_GET',          $this->request->isGet());
        defined('IS_AJAX')          or define('IS_AJAX',         $this->request->isAjax());
        defined('MODULE_NAME')      or define('MODULE_NAME',     $this->request->module());
        defined('CONTROLLER_NAME')  or define('CONTROLLER_NAME', $this->request->controller());
        defined('ACTION_NAME')      or define('ACTION_NAME',     $this->request->action());
        defined('URL')              or define('URL',             strtolower($this->request->controller() . '/' . $this->request->action()));
        defined('URL_MODULE')       or define('URL_MODULE',      strtolower($this->request->module()) . '/' . URL);
        
        $this->param = $this->request->param();
        $this->post = $this->request->post();
    }
    
    /**
     * 系统跳转
     * array _data  里面包含1-4个值  分别为 status 状态  | message 消息 | url 链接 / data 数据
     * 分页面跳转与ajax/app调用
     * exam:
        or
        jump(['error','操作失败','Member/index',$data])
        or
        jump([40044,'用户未登录'])
        or
        jump([0,'登录成功',['token'=>'a46awfa1wf1aw6gawf']])
        or
        jump(40040,'操作非法',['token'=>'a46awfa1wf1aw6gawf'])

        说明： 
            页面跳转（web）   code为0表示成功     返回数据格式：[0,"操作成功","Index/index",[]]                       主要用于pc、微信、mobile
            api接口           code为0表示成功     返回数据格式：[0,"操作成功",['token'=>'a46awfa1wf1aw6gawf']]        主要用于app
     */
    final protected function jump($_data = [])
    {

        if(!is_array($_data) || count($_data)>4) return null;

        $default_data=[0,'',null,null];

        list($status, $message, $url,$data)=$_data+$default_data;
        
        $success  = RESULT_SUCCESS;
        $error    = RESULT_ERROR;
        $redirect = RESULT_REDIRECT;

        // status为数字的情况 写api

        if(is_numeric($status)){
            
            die(json(['code'=>$status,"msg"=>$message,"data"=>$url])->send());

        }else{
            // 分配跳转类型
            switch ($status) {
                case $success  :$this->$success($message, $url, $data);
                case $error    :$this->$error($message, $url, $data);
                case $redirect :$this->$redirect($url, $data);
                default        :return $data;
            }
        }
    }
    /**
     * 系统跳转
     */
    final protected function tip($status = '', $message = '操作成功', $url = null, $data = '')
    {
        $this->view->engine->layout(false);

        is_array($status) && list($status, $message, $url) = $status;
        
        die($this->fetch(THINK_PATH . 'tpl' . DS . 'public_jump.tpl',['title'=>$message,'url'=>$url]));
    }
    /*
    * ajax数据操作
    */
    public function ajaxdata(){
        IS_AJAX && $this->jump(Core::loadModel("ControllerBase","common","logic")->ajaxdata($this->param));
    }
    /*
    * 空操作
    */
    public function _empty(){
        $this->tip([0,"你访问的页面不存在",null]);
    }
    
    /*
        上传图片
     */
    public function upload(){
        config("default_return_type","json");
        $this->jump(Core::loadModel("ControllerBase","common","logic")->upload());
    }
    /**
    * kindeditor 的上传方式
    */
    public function kd_upload(){

        $result = Core::loadModel("ControllerBase","common","logic")->kd_upload();

        die(json_encode($result));

    }
}
