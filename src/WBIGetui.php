<?php

namespace Jemoker\GetuiPush;


use IGeTui;
use IGtSingleMessage;
use IGtTarget;
use IGtAppMessage;
use IGtTransmissionTemplate;
require __DIR__.'/IGeTui.php';

class WBIGetui
{
	//单推接口案例
	public static function pushMessageToSingle($cid, $content, $payload = '')
	{
		$igt = new IGeTui('http://sdk.open.api.igexin.com/apiex.htm', config('getui-push.ios.key'), config('getui-push.ios.secret'));

		$template = self::IGtTransmissionTemplateDemo($content, $payload);

		//个推信息体
		$message = new IGtSingleMessage();

		$message->set_isOffline(true);//是否离线
		$message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
		$message->set_data($template);//设置推送消息类型

		//接收方
		$target = new IGtTarget();
		$target->set_appId(config('getui-push.ios.id'));
		$target->set_clientId($cid);

		$rep = $igt->pushMessageToSingle($message, $target);
	}

	//多推接口案例
	public static function pushMessageToList($targets, $content, $payload = '')
	{
		putenv("needDetails=true");
		$igt = new IGeTui('http://sdk.open.api.igexin.com/apiex.htm', config('getui-push.ios.key'), config('getui-push.ios.secret'));

		$template = self::IGtTransmissionTemplateDemo($content, $payload);

		//个推信息体
		$message = new IGtSingleMessage();

		$message->set_isOffline(true);//是否离线
		$message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
		$message->set_data($template);//设置推送消息类型

		$contentId = $igt->getContentId($message);

		//接收方1
		$targetList = array();
		foreach ($targets as $cid) {
			$target = new IGtTarget();
			$target->set_appId(config('getui-push.ios.id'));
			$target->set_clientId($cid);

			$targetList[] = $target;
		}

		$rep = $igt->pushMessageToList($contentId, $targetList);
	}

	//群推接口案例
	public static function pushMessageToApp($content, $payload = '', $provinces = array(), $tags = array())
	{
		$igt = new IGeTui('http://sdk.open.api.igexin.com/apiex.htm', config('getui-push.ios.key'), config('getui-push.ios.secret'));

		$template = self::IGtTransmissionTemplateDemo($content, $payload);

		//个推信息体
		//基于应用消息体
		$message = new IGtAppMessage();

		$message->set_isOffline(true);
		$message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
		$message->set_data($template);


		$message->set_appIdList(array(config('getui-push.ios.id')));
		$message->set_phoneTypeList(array('IOS'));
		if ($provinces) $message->set_provinceList($provinces);
		if ($tags) $message->set_tagList($tags);

		$rep = $igt->pushMessageToApp($message);
	}

	public static function IGtTransmissionTemplateDemo($content, $payload)
	{
		$template = new IGtTransmissionTemplate();
		$template->set_appId(config('getui-push.ios.id'));//应用appid
		$template->set_appkey(config('getui-push.ios.key'));//应用appkey
		$template->set_transmissionType(1);//透传消息类型
		$template->set_transmissionContent($payload);//透传内容
		//iOS推送需要设置的pushInfo字段
		$template->set_pushInfo("", 1, $content, "", $payload, "", "", "");
		return $template;
	}
}

?>
