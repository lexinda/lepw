<?php
namespace Home\Controller;
use Think\Controller;
use Think\Log;
class BaseController extends Controller {
	
    public function get_curl_json($url){
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	$result = curl_exec($ch);
    	if(curl_errno($ch)){
    		print_r(curl_error($ch));
    	}
    	curl_close($ch);
    	return json_decode($result,TRUE);
    }
    
    public function post_curl_json($url,$post_data){
//     	$url = "http://localhost/web_services.php";
//     	$post_data = array ("username" => "bob","key" => "12345");
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	$result = curl_exec($ch);
    	if(curl_errno($ch)){
    		print_r(curl_error($ch));
    	}
    	curl_close($ch);
    	return json_decode($result,TRUE);
    }
}