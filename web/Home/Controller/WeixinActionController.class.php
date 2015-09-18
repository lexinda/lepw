<?php
namespace Home\Controller;
use Think\Controller;
use Think\Log;
use Think\Controller\JsonRpcController;
class WeixinActionController extends BaseController {
	
    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "寰蒋闆呴粦"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover,{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>娆㈣繋浣跨敤 <b>ThinkPHP</b>锛�</p><br/>鐗堟湰 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }
    
    //获取access_token
    public function getAccessToken(){
    	//获取缓存
    	$accessToken = S('accessToken');
    	if (empty($accessToken)){
    		$tokenUrl = sprintf(C('TOKENURL'),C('APP_ID'),C('APP_SECRET'));
    		//{"access_token":"ACCESS_TOKEN","expires_in":7200}
    		$data = $this->get_curl_json($tokenUrl);
    		S('accessToken',$data['access_token'],7200);
    		$accessToken = S('accessToken');
    		return $accessToken;
     	}else{
    		return $accessToken;
    	}
    }
    
    //获取微信IP
    public function getWebchatIp(){
    	//{"ip_list":["127.0.0.1","127.0.0.1"]}
    	$url = sprintf(C('WEBCHAT_IP'),$this->getAccessToken());
    	$data = $this->get_curl_json($url);
    	echo json_encode($data['ip_list']);
    	
    }
    
}