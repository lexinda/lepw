<?php
namespace Home\Controller;
use Think\Controller;
use Think\Log;
class WeixinController extends Controller {
    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover,{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }
    
    public function weixin(){
    	header("Content-Type:text/html;charset=utf-8");
    	if (IS_GET){
    		$echoStr = I('get.echostr');
    		if($this->checkSignature()){
    			header('content-type:text');
    			echo $echoStr;
    		}
    	}else{
    		$this->responseMsg();
    	}
    }
    
    //验证微信地址
    public function checkSignature(){
    	$signature = I('get.signature');
    	$timestamp =I('get.timestamp');
    	$nonce = I('get.nonce');

        $token = C('TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    
    //接收微信消息
    public function responseMsg(){
    	$postStr = I('globals.HTTP_RAW_POST_DATA');
    	$postStr = htmlspecialchars_decode($postStr);
    	Log::write('postStr'.$postStr);
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $messageType = $postObj->MsgType;
            Log::write('messageType'.$messageType);
            if($messageType=='event'){
            	Log::write('event');
            	$textTpl = $this->textMessage();
            	$event = $postObj->Event;
            	//关注
            	if($event=='subscribe'){
            		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, '欢迎来到乐信达！');
            		echo $resultStr;
            	}elseif ($event=='unsubscribe'){//取消关注
            		
            	}elseif ($event=='CLICK'){//点击菜单关注
            		$eventKey = $postObj->EventKey;
            		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, '您点击了'.$eventKey);
            		echo $resultStr;
            	}
            }else{
            	$newsItem = $this->newsItemMessage();
            	$title = '苹果产品信息查询';
            	$description = '序列号：USE IMEI NUMBER
            				IMEI号：358031058974471
            				设备名称：iPhone 5C
            				设备颜色：
            				设备容量：
            				激活状态：已激活
            				电话支持：未过期[2014-01-13]
            				硬件保修：未过期[2014-10-14]
            				生产工厂：中国';
            	$picUrl = 'http://www.doucube.com/weixin/weather/icon/banner.jpg';
            	$newsItemStr = sprintf($newsItem,$title,$description,$picUrl,'');
            	$textTpl = $this->newsMessage();
            	if($keyword == "?" || $keyword == "？")
            	{
            		$contentStr = date("Y-m-d H:i:s",time());
            		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, '1', $newsItemStr);
            		echo $resultStr;
            	}
            }
        }else{
        	echo '123';
        }
    }
    
    /*文字信息
     *FromUserName 消息发送方
     *ToUserName 消息接收方
     *CreateTime 消息创建时间
     *MsgType 消息类型，文本消息必须填写text
     *Content 消息内容，大小限制在2048字节，字段为空为不合法请求
     *FuncFlag 星标字段
     */
    public function textMessage(){
    	$textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
    	return $textTpl;
    }
    
    /*
     * 音乐消息
     * ToUserName     接收方帐号（收到的OpenID）
		FromUserName     开发者微信号
		CreateTime     消息创建时间
		MsgType          消息类型，此处为music
		    Title       音乐标题
		    Description 音乐描述
		    MusicUrl     音乐链接
		    HQMusicUrl     高质量音乐链接，WIFI环境优先使用该链接播放音乐
		FuncFlag     位0x0001被标志时，星标刚收到的消息。
     */
    public function musicMessage(){
    	$textTpl = "<xml>
					    <ToUserName><![CDATA[%s]]></ToUserName>
					    <FromUserName><![CDATA[%s]]></FromUserName>
					    <CreateTime>%s</CreateTime>
					    <MsgType><![CDATA[%s]]></MsgType>
					    <Music>
					        <Title><![CDATA%s]]></Title>
					        <Description><![CDATA[%s]]></Description>
					        <MusicUrl><![CDATA[%s]]></MusicUrl>
					        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
					    </Music>
					    <FuncFlag>0</FuncFlag>
					</xml>";
    	return $textTpl;
    }
    
    /*图文消息
     *FromUserName 消息发送方
 	 *ToUserName 消息接收方
 	 *CreateTime 消息创建时间
 	 *MsgType 消息类型，图文消息必须填写news
 	 *Content 消息内容，图文消息可填空
 	 *ArticleCount 图文消息个数，限制为10条以内
 	 *Articles 多条图文消息信息，默认第一个item为大图
  	 *Title 图文消息标题
  	 *Description 图文消息描述
  	 *PicUrl 图片链接，支持JPG、PNG格式，较好的效果为大图640*320，小图80*80
  	 *Url 点击图文消息跳转链接
	 *FuncFlag 星标字段
	 *<xml>
	    <ToUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></ToUserName>
	    <FromUserName><![CDATA[gh_680bdefc8c5d]]></FromUserName>
	    <CreateTime>1359011899</CreateTime>
	    <MsgType><![CDATA[news]]></MsgType>
	    <Content><![CDATA[]]></Content>
	    <ArticleCount>1</ArticleCount>
	    <Articles>
	        <item>
	            <Title><![CDATA[[苹果产品信息查询]]></Title>
	            <Description><![CDATA[序列号：USE IMEI NUMBER
					IMEI号：358031058974471
					设备名称：iPhone 5C
					设备颜色：
					设备容量：
					激活状态：已激活
					电话支持：未过期[2014-01-13]
					硬件保修：未过期[2014-10-14]
					生产工厂：中国]]>
			    </Description>
			    <PicUrl><![CDATA[http://www.doucube.com/weixin/weather/icon/banner.jpg]]></PicUrl>
			    <Url><![CDATA[]]></Url>
			</item>
		</Articles>
		<FuncFlag>0</FuncFlag>
	</xml>
	<xml>
	    <ToUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></ToUserName>
	    <FromUserName><![CDATA[gh_680bdefc8c5d]]></FromUserName>
	    <CreateTime>1359011829</CreateTime>
	    <MsgType><![CDATA[news]]></MsgType>
	    <Content><![CDATA[]]></Content>
	    <ArticleCount>5</ArticleCount>
	    <Articles>
	        <item>
	            <Title><![CDATA[【深圳】天气实况 温度：3℃ 湿度：43﹪ 风速：西南风2级]]></Title>
	            <Description><![CDATA[]]></Description>
				<PicUrl><![CDATA[http://www.doucube.com/weixin/weather/icon/banner.jpg]]></PicUrl>
	            <Url><![CDATA[]]></Url>
	        </item>
	        <item>
	            <Title><![CDATA[06月24日 周四 2℃~-7℃ 晴 北风3-4级转东南风小于3级]]></Title>
	            <Description><![CDATA[]]></Description>
	            <PicUrl><![CDATA[http://www.doucube.com/weixin/weather/icon/d00.gif]]></PicUrl>
	            <Url><![CDATA[]]></Url>
	        </item>
	        <item>
	            <Title><![CDATA[06月25日 周五 -1℃~-8℃ 晴 东南风小于3级转东北风3-4级]]></Title>
	            <Description><![CDATA[]]></Description>
	    		<PicUrl><![CDATA[http://www.doucube.com/weixin/weather/icon/d00.gif]]></PicUrl>
	            <Url><![CDATA[]]></Url>
	        </item>
	        <item>
	            <Title><![CDATA[06月26日 周六 -1℃~-7℃ 多云 东北风3-4级转东南风小于3级]]></Title>
	            <Description><![CDATA[]]></Description>
				<PicUrl><![CDATA[http://www.doucube.com/weixin/weather/icon/d01.gif]]></PicUrl>
	            <Url><![CDATA[]]></Url>
	        </item>
	        <item>
	            <Title><![CDATA[06月27日 周日 0℃~-6℃ 多云 东南风小于3级转东北风3-4级]]></Title>
	            <Description><![CDATA[]]></Description>
				<PicUrl><![CDATA[http://www.doucube.com/weixin/weather/icon/d01.gif]]></PicUrl>
	            <Url><![CDATA[]]></Url>
	        </item>
	    </Articles>
	    <FuncFlag>0</FuncFlag>
	</xml>
     */
    public function newsMessage(){
    	$textTpl = "<xml>
    					<ToUserName><![CDATA[%s]]></ToUserName>
    					<FromUserName><![CDATA[%s]]></FromUserName>
    					<CreateTime>%s</CreateTime>
    					<MsgType><![CDATA[news]]></MsgType>
    					<Content><![CDATA[]]></Content>
    					<ArticleCount>%s</ArticleCount>
    					<Articles>%s</Articles>
    					<FuncFlag>0</FuncFlag>
    				</xml>";
    	return $textTpl;
    }
    
    public function newsItemMessage(){
    	$textTpl = "<item>
		    			<Title><![CDATA[[%s]]></Title>
		    			<Description><![CDATA[%s]]></Description>
		    			<PicUrl><![CDATA[%s]]></PicUrl>
		    			<Url><![CDATA[]]></Url>
	    			</item>";
    	return $textTpl;
    }
    
    /*
     * 事件
     * 关注事件
     * <xml>
		    <ToUserName><![CDATA[gh_b629c48b653e]]></ToUserName>
		    <FromUserName><![CDATA[ollB4jv7LA3tydjviJp5V9qTU_kA]]></FromUserName>
		    <CreateTime>1372307736</CreateTime>
		    <MsgType><![CDATA[event]]></MsgType>
		    <Event><![CDATA[subscribe]]></Event>
		    <EventKey><![CDATA[]]></EventKey>
		</xml>
	 *取消关注
	 *<xml>
	    <ToUserName><![CDATA[gh_b629c48b653e]]></ToUserName>
	    <FromUserName><![CDATA[ollB4jqgdO_cRnVXk_wRnSywgtQ8]]></FromUserName>
	    <CreateTime>1372309890</CreateTime>
	    <MsgType><![CDATA[event]]></MsgType>
	    <Event><![CDATA[unsubscribe]]></Event>
	    <EventKey><![CDATA[]]></EventKey>
	</xml>
	*菜单点击
	*<xml>
	    <ToUserName><![CDATA[gh_680bdefc8c5d]]></ToUserName>
	    <FromUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></FromUserName>
	    <CreateTime>1377886191</CreateTime>
	    <MsgType><![CDATA[event]]></MsgType>
	    <Event><![CDATA[CLICK]]></Event>
	    <EventKey><![CDATA[天气深圳]]></EventKey>
	</xml>
     * 收到消息格式
     * 文字
     * ToUserName 消息接收方微信号，一般为公众平台账号微信号
		FromUserName 消息发送方微信号
		CreateTime 消息创建时间
		MsgType 消息类型；文本消息为text
		Content 消息内容
		MsgId 消息ID号
     * <xml>
		 <ToUserName><![CDATA[gh_680bdefc8c5d]]></ToUserName>
		 <FromUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></FromUserName>
		 <CreateTime>1359028446</CreateTime>
		 <MsgType><![CDATA[text]]></MsgType>
		 <Content><![CDATA[测试文字]]></Content>
		 <MsgId>5836982729904121631</MsgId>
		</xml>
	 *表情
	 *<xml><ToUserName><![CDATA[gh_680bdefc8c5d]]></ToUserName>
		<FromUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></FromUserName>
		<CreateTime>1359044526</CreateTime>
		<MsgType><![CDATA[text]]></MsgType>
		<Content><![CDATA[/::)/::~/::B/::|/:8-)]]></Content>
		<MsgId>5837051792978241864</MsgId>
		</xml>
	*图片
	*ToUserName 消息接收方微信号，一般为公众平台账号微信号
	FromUserName 消息发送方微信号
	CreateTime 消息创建时间
	MsgType 消息类型；图片消息为image
	PicUrl 图片链接地址，可以用HTTP GET获取
	MsgId 消息ID号
	*<xml><ToUserName><![CDATA[gh_680bdefc8c5d]]></ToUserName>
		<FromUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></FromUserName>
		<CreateTime>1359028479</CreateTime>
		<MsgType><![CDATA[image]]></MsgType>
		<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/L4qjYtOibummHn90t1mnaibYiaR8ljyicF3MW7XX3BLp1qZgUb7CtZ0DxqYFI4uAQH1FWs3hUicpibjF0pOqLEQyDMlg/0]]></PicUrl>
		<MsgId>5836982871638042400</MsgId>
		<MediaId><![CDATA[PGKsO3LAgbVTsFYO7FGu51KUYa07D0C_Nozz2fn1z6VYtHOsF59PTFl0vagGxkVH]]></MediaId>
		</xml>
	*语音
	*ToUserName 消息接收方微信号，一般为公众平台账号微信号
	FromUserName 消息发送方微信号
	CreateTime 消息创建时间
	MsgType 消息类型；语音消息为voice
	MediaId 媒体ID
	Format 语音格式，这里为amr
	MsgId 消息ID号
	*<xml>
	    <ToUserName><![CDATA[gh_d035bb259cf5]]></ToUserName>
	    <FromUserName><![CDATA[owEUGj4BW8yeWRvyEERiVGKwAF1Q]]></FromUserName>
	    <CreateTime>1364883809</CreateTime>
	    <MsgType><![CDATA[voice]]></MsgType>
	    <MediaId><![CDATA[JfmCezZ3Cwp0FwUvMADwwhvp-XScuvpictubpw0c6ALyA8tj3HLU4PoXzMpIY72P]]></MediaId>
	    <Format><![CDATA[amr]]></Format>
	    <MsgId>5862131322594912688</MsgId>
	</xml>
	*视频
	*ToUserName 消息接收方微信号，一般为公众平台账号微信号
	FromUserName 消息发送方微信号
	CreateTime 消息创建时间
	MsgType 消息类型；视频消息为video
	MediaId 媒体ID
	ThumbMediaId 媒体缩略ID？
	MsgId 消息ID号
	*xml><ToUserName><![CDATA[gh_680bdefc8c5d]]></ToUserName>
	<FromUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></FromUserName>
	<CreateTime>1359028186</CreateTime>
	<MsgType><![CDATA[video]]></MsgType>
	<MediaId><![CDATA[DBVFRIj29LB2hxuYpc0R6VLyxwgyCHZPbRj_IIs6YaGhutyXUKtFSDcSCPeoqUYr]]></MediaId>
	<ThumbMediaId><![CDATA[mxUJ5gcCeesJwx2T9qsk62YzIclCP_HnRdfTQcojlPeT2G9Q3d22UkSLyBFLZ01J]]></ThumbMediaId>
	<MsgId>5836981613212624665</MsgId>
	</xml>
	*位置
	* ToUserName 消息接收方微信号，一般为公众平台账号微信号
	 FromUserName 消息发送方微信号
	 CreateTime 消息创建时间
	 MsgType 消息类型，地理位置为location
	 Location_X 地理位置纬度
	 Location_Y 地理位置经度
	 Scale 地图缩放大小
	 Label 地理位置信息
	 MsgId 消息ID号
	*<xml>
		<ToUserName><![CDATA[gh_680bdefc8c5d]]></ToUserName>
		<FromUserName><![CDATA[oIDrpjqASyTPnxRmpS9O_ruZGsfk]]></FromUserName>
		<CreateTime>1359036619</CreateTime>
		<MsgType><![CDATA[location]]></MsgType>
		<Location_X>22.539968</Location_X>
		<Location_Y>113.954980</Location_Y>
		<Scale>16</Scale>
		<Label><![CDATA[中国广东省深圳市南山区华侨城深南大道9789号 邮政编码: 518057]]></Label>
		<MsgId>5837017832671832047</MsgId>
	</xml>
	*链接
	*ToUserName 消息接收方微信号，一般为公众平台账号微信号
	 FromUserName 消息发送方微信号
	 CreateTime 消息创建时间
	 MsgType 消息类型，链接为link
	 Title 图文消息标题
	 Description 图文消息描述
	 Url 点击图文消息跳转链接
	 MsgId 消息ID号
	*<xml>
	<ToUserName><![CDATA[gh_680bdefc8c5d]]></ToUserName> 
	<FromUserName><![CDATA[oIDrpjl2LYdfTAM-oxDgB4XZcnc8]]></FromUserName> 
	<CreateTime>1359709372</CreateTime> 
	<MsgType><![CDATA[link]]></MsgType> 
	<Title><![CDATA[微信公众平台开发者的江湖]]></Title> 
	<Description><![CDATA[陈坤的微信公众号这段时间大火，大家..]]></Description> 
	<Url><![CDATA[http://israel.duapp.com/web/photo.php]]></Url> 
	<MsgId>5839907284805129867</MsgId> 
	</xml>
     */
    
}