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
namespace app\backend\logic;
use \tpfcore\Core;
use \tpfcore\util\Config;


class Template extends AdminBase
{

    public function  editSetting($data){
    	foreach ($data['configs'] as $key => $value) {
			$update_config[$key]=$value;
		}

		unset($data['configs']);

		Config::updateConfig(APP_PATH."extra/config.php",$update_config);
		\think\Cache::clear();		//清空cache
    	array_map('unlink',glob(TEMP_PATH.DS.'*.php')); 	//清空temp
    	$path = glob(LOG_PATH.'/*'); 
		foreach ($path as $item) { 
			array_map('unlink',glob($item.DS.'*.*')); 
			rmdir($item);
		}
		return [RESULT_SUCCESS, '保存成功', url('Template/index')];
    }
	
	
	public  function delTheme($data) {
	$file = TPLS_PATH . $data['file'];   
	if($data['file'] == config('config.DEFAULT_THEME')){
            return [RESULT_ERROR, $data['file']."模板正在使用不能删除", null];
	}
	$ret = $this->DeleteFile($file);
	return [RESULT_SUCCESS, '操作成功', url('Template/index')]?[RESULT_SUCCESS, '删除成功', url('Template/index')]:[RESULT_ERROR, '删除失败', url('Template/index')];
	}

   private function DeleteFile($file) {	
	if (empty($file))
        return false;
    if (@is_file($file))
        return @unlink($file);
    $ret = true;
    if ($handle = @opendir($file)) {
        while ($filename = @readdir($handle)) {
            if ($filename == '.' || $filename == '..')
                continue;
            if (!$this->DeleteFile($file . '/' . $filename))
                $ret = false;
        }
    } else {
        $ret = false;
    }
    @closedir($handle);
    if (file_exists($file) && !rmdir($file)) {
        $ret = false;
    }	
	return $ret;
   }
	
}