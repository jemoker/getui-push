<?php

namespace Jemoker\GetuiPush;

use IGeTui;
use IGtNotificationTemplate;
use IGtSingleMessage;
use IGtTarget;

require __DIR__.'/IGeTui.php';

class WBAGetui
{
	//单推接口案例
	public static function pushMessageToSingle($cid, $title, $content, $type, $payload = '',  $url = ''){
		require_once(dirname(__FILE__). '/' . 'IGeTui.php');
    	$igt = new IGeTui('http://sdk.open.api.igexin.com/apiex.htm', config('getui-push.android.key'), config('getui-push.android.secret'));
    	
    	if ($type == 1) {//通知栏
    		$template = self::IGtNotificationTemplateDemo($title, $content, $payload);
    	}elseif ($type == 2)//透传
    	{
    		$template = self::IGtTransmissionTemplateDemo($payload);
    	}elseif ($type == 3) //转到url
    	{
    		$template = self::IGtLinkTemplateDemo($title, $content, $url);
    	}
	    //个推信息体
		$message = new IGtSingleMessage();
	
		$message->set_isOffline(true);//是否离线
		$message->set_offlineExpireTime(3600*12*1000);//离线时间
		$message->set_data($template);//设置推送消息类型
	
		//接收方
		$target = new IGtTarget();
		$target->set_appId(config('getui-push.android.id'));
		$target->set_clientId($cid);

		$rep = $igt->pushMessageToSingle($message,$target);
	}
	
	//多推接口案例
	public static function pushMessageToList($targets, $title, $content, $type, $payload = '',  $url = ''){
		putenv("needDetails=true");
		$igt = new IGeTui('http://sdk.open.api.igexin.com/apiex.htm', config('getui-push.android.key'), config('getui-push.android.secret'));
		
		if ($type == 1) {//通知栏
    		$template = self::IGtNotificationTemplateDemo($title, $content, $payload);
    	}elseif ($type == 2)//透传
    	{
    		$template = self::IGtTransmissionTemplateDemo($payload);
    	}elseif ($type == 3) //转到url
    	{
    		$template = self::IGtLinkTemplateDemo($title, $content, $url);
    	}
		
		//个推信息体
		$message = new IGtSingleMessage();
	
		$message->set_isOffline(true);//是否离线
		$message->set_offlineExpireTime(3600*12*1000);//离线时间
		$message->set_data($template);//设置推送消息类型
		
		$contentId = $igt->getContentId($message);
	
		//接收方1	
		$targetList = array();
		foreach ($targets as $cid)
		{
			$target = new IGtTarget();
			$target->set_appId(config('getui-push.android.id'));
			$target->set_clientId($cid);
			
			$targetList[] = $target;
		}
		$rep = $igt->pushMessageToList($contentId, $targetList);
	}
	
	//群推接口案例
	public static function pushMessageToApp($title, $content, $type, $payload = '',  $url = '', $provinces=array(), $tags = array()){
		$igt = new IGeTui('http://sdk.open.api.igexin.com/apiex.htm', config('getui-push.android.key'), config('getui-push.android.secret'));
		
		if ($type == 1) {//通知栏
    		$template = self::IGtNotificationTemplateDemo($title, $content, $payload);
    	}elseif ($type == 2)//透传
    	{
    		$template = self::IGtTransmissionTemplateDemo($payload);
    	}elseif ($type == 3) //转到url
    	{
    		$template = self::IGtLinkTemplateDemo($title, $content, $url);
    	}
    	
		//个推信息体
		//基于应用消息体
		$message = new \IGtAppMessage();
	
		$message->set_isOffline(true);
		$message->set_offlineExpireTime(3600*12*1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
		$message->set_data($template);
	 
		$message->set_appIdList(array(config('getui-push.android.id')));
		$message->set_phoneTypeList(array('ANDROID'));
		if ($provinces) $message->set_provinceList($provinces);
		if ($tags) $message->set_tagList($tags);
	
		$rep = $igt->pushMessageToApp($message);
	}
	
	//通知栏
	public static function IGtNotificationTemplateDemo($title, $content, $payload){
        $template =  new IGtNotificationTemplate();
        $template->set_appId(config('getui-push.android.id'));//应用appid
        $template->set_appkey(config('getui-push.android.key'));//应用appkey
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
		$template->set_logo("ic_launcher.png");//通知栏图标
        $template->set_transmissionType(2);
        $template->set_transmissionContent($payload);
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        return $template;
	}
	
	//透传
	public static function IGtTransmissionTemplateDemo($payload){
		$template =  new \IGtTransmissionTemplate();
		$template->set_appId(config('getui-push.android.id'));//应用appid
		$template->set_appkey(config('getui-push.android.key'));//应用appkey
		$template->set_transmissionType(1);//透传消息类型
		$template->set_transmissionContent($payload);//透传内容
		return $template;
	}
	
	//打开链接
	public static function IGtLinkTemplateDemo($title, $content, $url){
		$template =  new \IGtLinkTemplate();
		$template ->set_appId(config('getui-push.android.id'));//应用appid
		$template ->set_appkey(config('getui-push.android.key'));//应用appkey
		$template ->set_title($title);//通知栏标题
		$template ->set_logo("ic_launcher.png");//通知栏图标
		$template ->set_text($content);//通知栏内容
		$template ->set_isRing(true);//是否响铃
		$template ->set_isVibrate(true);//是否震动
		$template ->set_isClearable(true);//通知栏是否可清除
		$template ->set_url($url);//打开连接地址
		return $template;
	}
}

?>
