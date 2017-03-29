<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Libraries\Contracts\GetWeiboCookie;

use Config;
use Storage;

class AuthorizeController extends Controller
{
	
    //
    public function index()
    {
    	###判断cookie是否有效
    	return view("admin/auth");
    }
    
    //将用户名和密码录入临时文件，
    public function setConfig(Request $request)
    {
    	$this->validate($request, [
    			'username' => 'required|max:255',
    			'password' => 'required',
    	]);
    	
    	$content = [
    			'USERNAME' => base64_encode($request->get('username')),
    			'PASSWORD' => $request->get('password'),
    	];
    	
    	$savePath = 'wbcookie/config.inc';
    	$bytes = Storage::put($savePath, json_encode($content));
    	if(!Storage::exists($savePath)){
    		return redirect()->back()->withInput()->withErrors('无法设置授权信息');
    	}   	
    	return view("admin/set_config");
    }
    
    
    /**
     * 根据配置文件获得预登陆参数
     * 跳转到密码加密页
     */
    public function getPreParam()
    {
    	try {
    		$getCookie = new GetWeiboCookie();
    		//获得配置
    		$getCookie->getConfig();
    		//获得预登录参数，跳转到预登陆地址
    		$getCookie->getPreUrl();
    	}
    	catch (\Exception $e){
//     		abort(504,$e->getMessage());
    		return Redirect ( "admin/authorize/fail" );
    	}
    	return Redirect("/admin/authorize/getRsaPwd");
    }

    
    /**
     * 根据传入参数，使用js算法获得加密后的密码
     */
    public function getRsaPwd(Request $request)
    {	
    	//获得预登陆配置
    	$savePath = 'wbcookie/prelogin.config.inc';
    	if(!Storage::exists($savePath)){
    		return false;
    	}
    	$preParam = json_decode(Storage::get($savePath), true);
    	
    	if(empty($preParam['servertime']))
      	{
       		return "参数错误!";
       	}
       	else{
       		$getCookie = new GetWeiboCookie();
       		$preParam['sp'] = $getCookie->getSp();
       		
    		header('Content-type:text/html;charset=utf-8');
    		echo "<script type='text/javascript' src='/js/jquery-1.10.2.min.js'></script>\n";
    		echo "<script type='text/javascript' src='/js/prelogin.js'></script>";
			echo <<<EOT
<script type="text/javascript">
	function getpass(pwd,servicetime,nonce,rsaPubkey){
		var RSAKey=new sinaSSOEncoder.RSAKey();
		RSAKey.setPublic(rsaPubkey,'10001');
		var password=RSAKey.encrypt([servicetime,nonce].join('\\t')+'\\n'+pwd);
		return password;
	}
    document.write('微博密码正在加密！！！');
 	var encrpt = getpass('{$preParam['sp']}', '{$preParam['servertime']}', '{$preParam['nonce']}', '{$preParam['pubkey']}' );
// 	document.write(encrpt);
	window.location.href='/admin/authorize/browserLogin/?sp='+encrpt;  
</script>"
EOT;
     		return ;
     	}
    }
    
    
    /**
     * 验证码提交页
     * @param Request $request
     * @return 
     */
    public function browserLogin(Request $request)
    {
    	$preParam = $this->getPreConfig();
    	if(empty($preParam))
    	{
    		return "参数错误!";
    	}
    	$preParam['sp'] = $request->get('sp');
    	
    	if(!isset($preParam['showpin'])){
    		$preParam['showpin'] = 1;
    	}    	
    	if($preParam['showpin'] == 1){    		
    		$randInt = rand(pow(10,(8-1)), pow(10,8)-1);
    		$preParam['doorImg'] = "http://login.sina.com.cn/cgi/pin.php?r={$randInt}&s=0&p={$preParam['pcid']}";
			return view ( "admin/browser_login", $preParam );
		} 
		else {
			// ##如果没有图片验证码
			$preParam ['door'] = '';
			if ($this->getCookiePack($preParam)) {
				return Redirect ( "admin/authorize/seccuss" );
			} else {
				return Redirect ( "admin/authorize/fail" );
			}	
    	}
    }
    
    
    /**
     * 接收加密后的密码和登录信息，获取cookie
     * @param Request $request
     * @return string
     */
    public function getCookie(Request $request)
    {
    	$preParam = $this->getPreConfig();
    	if(empty($preParam))
    	{
    		return "参数错误!";
    	}
    	
    	$preParam['sp']  = $request->get('sp');
    	$preParam['door']  = $request->get('door');
    	
    	if(empty($preParam['sp']))
    	{
    		return "参数错误!";
    	}
    	
    	if ($this->getCookiePack($preParam)) {
    		return Redirect ( "admin/authorize/seccuss" );
    	} else {
    		return Redirect ( "admin/authorize/fail" );
    	}
    }
    
    /**
     * 测试访问微博
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function setTestUrl()
    {
    	return view("admin/test_url");
    }

    public function getTestContent(Request $request)
    {
    	$this->validate($request, [
    			'wb_url' => 'required|max:255',
    	]);
    	$url = $request->get('wb_url');
    	//微博cookie
    	$cookieWeibo = storage_path()."/app/wbcookie/cookie_weibo.txt";
    	$cookieGet =  storage_path()."/app/wbcookie/cookie_curl.txt";
    	
    	if(!Storage::exists("wbcookie/cookie_weibo.txt")){
    		return view("admin/error", [ 'error' => '未获得授权信息，请重新登录！']);
    	}
    	
    	$wbLogin = new \App\Libraries\Classes\WeiboLogin();
    	$content = $wbLogin->getWBHtml($url, $cookieWeibo, $cookieGet);
    	echo $content;
    }
    
    
    
    /**
     * 获得预登陆配置
     */
    private function getPreConfig()
    {
	    //获得预登陆配置
	    $savePath = 'wbcookie/prelogin.config.inc';
	    if(!Storage::exists($savePath)){
	    	return false;
	    }
	    return json_decode(Storage::get($savePath), true);
    }
    
    
    /**
     * 封装登录
     */
    private function getCookiePack($preParam)
    {
    	$getCookie = new GetWeiboCookie();
    	try {
    		if ($getCookie->getCookie ( $preParam )) {
    			return true;
    		} else {
    			return false;
    		}
    	}
    	catch (\Exception $e){
    		//abort(505, $e->getMessage());
    		return false;
    	} 
    }
}
