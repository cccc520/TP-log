<?php
// +----------------------------------------------------------------------
// | Author: yaoyihong <510974211@qq.com>
// +----------------------------------------------------------------------
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
namespace tpfcore\util;
use tpfcore\mail\PHPMailer;
class MailHandle{
	private $host;
	private $port;
	private $username;
	private $password;
	private $sitename;
	private $sendtype;
	public function __construct($host="",$port=0,$username="",$password="",$sitename="",$sendtype=1){
		$this->host=$host;
		$this->port=$port;
		$this->username=$username;
		$this->password=$password;
		$this->sitename=$sitename;
		$this->sendtype=$sendtype;
	}
	/**
	 * 系统邮件发送函数
	 * @param string $sendtype 邮件发送类型
	 * @param string $tomail 接收邮件者邮箱
	 * @param string $name 接收邮件者名称
	 * @param string $subject 邮件主题
	 * @param string $body 邮件内容
	 * @param string $attachment 附件列表
	 * @param boolean $is_ssl_validate 是否ssl安全验证 默认false
	 * @return boolean
	 * @author yaoyihong <510974211@qq.com>
	 */
	function sendMail($tomail, $toname, $subject = '', $body = '', $attachment = null , $is_ssl_validate = false) {
	    $mail = new PHPMailer();           //实例化PHPMailer对象
	    $mail->CharSet = 'UTF-8'; 
		//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		if($this->sendtype = 1)
		{
		$mail->IsSMTP();// 设定使用SMTP服务
		}else{
		$mail->IsMail();// 设定使用MAIL服务
		}
	    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
	    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
		
	    if($is_ssl_validate) $mail->SMTPSecure = 'ssl';          // 使用安全协议
		
	    $mail->Host = $this->host; 			// SMTP 服务器		smtp.exmail.qq.com
	    $mail->Port = $this->port;                  // SMTP服务器的端口号		465		发送邮件服务器：smtp.exmail.qq.com (端口 25)，使用SSL，端口号465或587
	    $mail->Username = $this->username;    // SMTP服务器用户名
	    $mail->Password = $this->password;     // SMTP服务器密码
	    $mail->SetFrom($this->username,$this->sitename);
	    $replyEmail = $this->username;      //留空则为发件人EMAIL
	    $replyName = '';                    //回复名称（留空则为发件人名称）
	    $mail->AddReplyTo($replyEmail, $replyName);
	    $mail->Subject = $subject;
	    $mail->MsgHTML($body);
	    $mail->AddAddress($tomail, $toname);
	    if (is_array($attachment)) { // 添加附件
	        foreach ($attachment as $file) {
	            is_file($file) && $mail->AddAttachment($file);
	        }
	    }
	    return $mail->Send() ? true : $mail->ErrorInfo;
	}

}
?>