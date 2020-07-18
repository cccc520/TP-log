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
/**
 *  幻灯片逻辑
 */
class SlideCat extends AdminBase
{
	public function saveSlideCat($data){
        $validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }

		$last_id=Core::loadModel($this->name)->saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url("SlideCat/index")];
        }else{
        	return [RESULT_ERROR, '操作失败', url("SlideCat/index")];
        }
	}
	public function delSlideCat($data){
		return Core::loadModel($this->name)->deleteObject($data,true)?[RESULT_SUCCESS, '删除成功', url("SlideCat/index")]:[RESULT_ERROR, '删除失败', url("SlideCat/index")];
	}
	public function getSlideCat($data=[]){
		return self::getList($data);
	}
}