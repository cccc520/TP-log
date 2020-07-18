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
 *  会员逻辑
 */
class Member extends AdminBase
{
	protected $table;
	public function __construct(){
		$this->table=config('database.prefix').'user';
		$this->name="User";
	}
	public function uppwd($data){
		$validate=\think\Loader::validate("Member");
		$validate_result = $validate->scene('update')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
        if(!$this->checkPassword($data)){
        	return [RESULT_ERROR, '旧密码不正确', null];
        }
        if(self::updateObject(['username'=>\think\Session::get("backend_author_sign")['username']],["password"=>"###".md5($data['password'].DATA_ENCRYPT_KEY)])){
        	\think\Session::delete("backend_author_sign");
        	return [RESULT_SUCCESS, '修改密码成功，请重新登录', url('User/login')];
        } 
	}
	public function upUserInfo($data){
		$validate=\think\Loader::validate("Member");
		$validate_result = $validate->scene('upinfo')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
        $result=self::updateObject(['id'=>\think\Session::get("backend_author_sign")['userid']],$data);
        if($result){
        	return [RESULT_SUCCESS, '修改资料成功', null];
        }else{
        	return [RESULT_ERROR, '操作失败', null];
        }
	}
	public function getInfo($where){
		return Core::loadModel("User")->getOneObject($where);
	}
	public function checkPassword($data){
		return self::getStatistics(['username'=>\think\Session::get("backend_author_sign")['username'],"password"=>"###".md5($data['old_password'].DATA_ENCRYPT_KEY)]);
	}
	
	
	public function getUserList($where = [], $field = true, $order = '', $is_paginate = true){
		$paginate_data = $is_paginate ? ['rows' => DB_LIST_ROWS] : false;
		return Core::loadModel("User")->getObject($where, $field, $order, $paginate_data);
	}
	
	
	
	public function addUser($data){
		$validate=\think\Loader::validate("Member");
		$validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
		$data['type'] = '0' ;
        $result=Core::loadModel($this->name)->saveObject($data);
		if($result){
			return [RESULT_SUCCESS, '操作成功', url('Member/index')];
		}else{
			return [RESULT_ERROR, '操作失败', url('Member/index')];
		}
	}
	
	public function delUser($data){
		if(is_array($data['id'])){
			$data['id'] = ['in',implode(',',$data['id'])];//批量删除
		}
		if($data['id']>1){
			$result = self::deleteObject($data,true);
			if($result){
			return [RESULT_SUCCESS, '操作成功', url('Member/index')];
		}else{
			return [RESULT_ERROR, '操作失败', url('Member/index')];
		}
		}
		return [RESULT_ERROR,"操作失败",url('Manager/index')];
	}
	
	
	public function addAdmin($data){
		$validate=\think\Loader::validate("Member");
		$validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
		$data['type'] = '1';
        $result=Core::loadModel($this->name)->saveObject($data);
		if($result){
			$data['role_id'] && self::updateObject(['username'=>$data['username']],['privs'=>Core::loadModel("Role")->getColumnValue(["id"=>input("role_id")],"privs")]);
			return [RESULT_SUCCESS, '操作成功', url('Member/admin')];
		}else{
			return [RESULT_ERROR, '操作失败', url('Member/admin')];
		}
	}
	
	public function delAdmin($data){
		return self::deleteObject("id={$data['id']} and id<>1",true)?[RESULT_SUCCESS,"操作成功",url("Member/admin")]:[RESULT_ERROR,'操作失败',url('Member/admin')];
	}
	
	public function editUser($data){
		$validate=\think\Loader::validate("Member");
		$validate_result = $validate->scene('edit')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
        $result=Core::loadModel($this->name)->saveObject($data);
		if($result){
			$data['role_id'] && self::updateObject(['id'=>$data['id']],['privs'=>Core::loadModel("Role")->getColumnValue(["id"=>input("role_id")],"privs")]);
			return [RESULT_SUCCESS, '操作成功', url('Member/admin')];
		}else{
			return [RESULT_ERROR, '操作失败', url('Member/admin')];
		}
	}
	

	
	
	
	
	
	public function ban($data){
		self::saveObject($data);
		return [RESULT_SUCCESS, '操作成功', null];
	}
	
	
	public function priv($data){
		$result=Core::loadModel($this->name)->saveObject($data);
		if($result){
			self::updateObject(["id"=>$data['id']],['role_id'=>0]);
			return [RESULT_SUCCESS, '操作成功', url('Member/admin')];
		}
		return [RESULT_ERROR, '操作失败', url('Member/admin')];
	}
}