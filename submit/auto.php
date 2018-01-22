<?php
class Get_Music{
	private static $_COOKIE = "默认Cookie";
	private static $_PROXY = "";
	private static $_REFERER_XIAMI = "http://www.xiami.com/";
	private static $_REFERER_NETEASE = "http://music.163.com/";
	private static $_PUBKEY = "65537";
	private static $_NONCE = "0CoJUm6Qyw8W8jud";
	private static $_MODULUS = "157794750267131502212476817800345498121872783333389747424011531025366277535262539913701806290766479189477533597854989606803194253978660329941980786072432806427833685472618792592200595694346872951301770580765135349259590167490536138082469680638514416594216629258349130257685001248172188325316586707301643237607";
	private static $_HEADER = array(
		'X-Real-IP: 118.88.88.88',
		'Accept-Language: zh-CN,zh;q=0.8,gl;q=0.6,zh-TW;q=0.4',
		'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'
	);

	public function curl_http($url, $useCookie, $referer=null, $get_header=null, $post=null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// Use Proxy for Overseas Host
		$proxy = isset($_POST["proxy"]) && $_POST["proxy"] ? $_POST["proxy"] : self::$_PROXY;
		if ($proxy){
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		// Use Cookie for xiami Suggested Playlist
		if ($useCookie){
			$cookie = isset($_POST["cookie"]) && $_POST["cookie"] ? $_POST["cookie"] : self::$_COOKIE;
			if (!preg_match('/member_auth=/i', $cookie)) $cookie = 'member_auth='.$cookie;
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_HTTPHEADER, self::$_HEADER);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000*5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ($get_header){
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
		}
		if ($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		for ($i=0; $i<=3; $i++){
			$result = curl_exec($ch);
			$error = curl_errno($ch);
			if (!$error) {
				break;
			}
		}
		curl_close($ch);
		if($error){
			return false;
		} else {
			return $result;
		}
	}
	public function get_xiami($sid, $type){
		if ($sid!=1){
			$info = explode("/", $sid);
			if (preg_match("/[a-zA-Z]/", $info[1])){
				$url = "http://www.xiami.com/".$sid;
				$pageindex = $this->curl_http($url, 0);
				preg_match("#link rel=\"canonical\" href=\"http://www.xiami.com/\w+/(\d+)\"#", $pageindex, $matches);
				if ($matches){
					$sid = $matches[1];
				} else {
					$data['info'] = '获取歌曲ID失败！';
					return $data;
				}
			} else {
				$sid = $info[1];
			}
		}
		$url = 'http://www.xiami.com/song/playlist/id/'.$sid.'/type/'.$type.'/cat/json';
		$useCookie = $type==9 ? 1 : 0;
		$result = json_decode($this->curl_http($url, $useCookie), true);
		//var_dump($result);exit;
		if (isset($result["data"]["trackList"])){
			$track = $result["data"]["trackList"][0];
			$name = $track["songName"];
			$artist = $track["singers"];
			$artist = str_replace(';',',',$artist);
			$album = $track["album_name"];
			$data = array("info"=>"success","name"=>$name,"artist"=>$artist,"album"=>$album);
		} else {
			$data['info'] = '解析失败！';
		}
		return $data;
	}
	public function get_netease($keyLink){
		$keywords = explode("?id=", $keyLink);
		$mid = $keywords[1];
		$url = "http://music.163.com/api/song/detail/?id=" . $mid . "&ids=[" . $mid . "]";
		$result = json_decode($this->curl_http($url, 0, self::$_REFERER_NETEASE), true);
		//var_dump($result);exit;
		if($result["code"]==200 && isset($result["songs"][0]) ){
			$cake = $result["songs"][0];
			$name = $cake["name"];
			$album = $cake["album"]["name"];
			$artistarr = array();
			foreach($cake["artists"] as $v){
				$artistarr[] = $v["name"];
			}
			$artist = implode(",",$artistarr);
			$data = array("info"=>'success',"name"=>$name,"artist"=>$artist,"album"=>$album);
		} else {
			$data['info'] = '获取歌曲链接失败！';
		}
		return $data;
	}
	// 虾米歌曲链接转换代码
	private function xiami_location($str){
		try{
			$a1=(int)$str{0};
			$a2=substr($str, 1);
			$a3=floor(strlen($a2) / $a1);
			$a4=strlen($a2) % $a1;
			$a5=array();
			$a6=0;
			$a7='';
			for(;$a6 < $a4; ++$a6){
				$a5[$a6]=substr($a2, ($a3 + 1) * $a6, ($a3 + 1));
			}
			for(;$a6 < $a1; ++$a6){
				$a5[$a6]=substr($a2, $a3 * ($a6 - $a4) + ($a3 + 1) * $a4, $a3);
			}
			for($i=0, $a5_0_length=strlen($a5[0]); $i < $a5_0_length; ++$i){
				for($j=0, $a5_length=count($a5); $j < $a5_length; ++$j){
					if (isset($a5[$j]{$i})) $a7.=$a5[$j]{$i};
				}
			}
			$a7=str_replace('^', '0', urldecode($a7));
			return $a7;
		} catch(Exception $e){
			return false;
		}
	}
}
$get_music = new Get_Music;
// 返回Ajax请求
if(isset($_GET["socool"])){
$data = array(
	'status' => 0,
	'info' => '请输入正确网址！'
);
if (isset($_GET["url"]) && $_GET["url"]){
	$url = $_GET["url"];
	if (preg_match('#/song/playlist/id/1/type/9#i', $url)){
		$sid = 1;
		$type = 9;
		$data = array_merge($data, $get_music->get_xiami($sid, $type));
	}
	elseif (preg_match('#/(demo/\w+)(\?*|)#i', $url, $matches)){
		$sid = $matches[1];
		$type = 0;
		$data = array_merge($data, $get_music->get_xiami($sid, $type));
	}
	elseif (preg_match('/xiami.com/i', $url)){
		if (preg_match('#/(song/\w+)(\?*|)#i', $url, $matches)){
			$sid = $matches[1];
			$type = 0;
		}
		$data = $get_music->get_xiami($sid, $type);
	}
	elseif (preg_match('/y.qq.com/i', $url)){
		$data['info'] = "(>_<)  QQ音乐解析暂不开源";
	}
	elseif (preg_match('/music.163.com/i', $url) || preg_match('/igame.163.com/i', $url)){
		if (preg_match('#/(\w+\?id=\d+)#i', $url, $matches)){
			$data = $get_music->get_netease($matches[1]);
		}
	}
	else{
		echo '歌曲链接错误';exit;
	}
}
//var_dump($data);
exit(json_encode($data));
}