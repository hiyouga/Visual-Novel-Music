<?php
header("Content-type;text/html;charset=utf-8");
require_once '../src/database.php';
if($_POST['key'] != 'hy'){
	echo "Wrong key!<br />三秒后返回上一页面<script>setTimeout(\"window.history.back(-1)\",3000)</script>";exit;
}
if($_POST['upmethod'] == '1'){//本地上传
	$target_path = '/home/ubuntu/website/hiyouga/html/vnmusic/music/' . substr(date("Y"),2) . date("m") . '/';
	if($res = mysqli_query($link,"SELECT MAX(MID) FROM music_full")){
		while($row = mysqli_fetch_row($res)){
			$num = $row[0] + 1;
		}
		mysqli_free_result($res);
	}
	if(!empty($_POST['mid'])){
		$target_filename = $_POST['mid'] . '-' . $_POST['quality'] . '.' . $_POST['format'];
	}else{
		$target_filename = $num . '-' . $_POST['quality'] . '.' . $_POST['format'];
	}
	$target_name = $target_path.$target_filename;
	//echo $target_name;
	$source_name = '/home/ubuntu/website/hiyouga/html/vnmusic/upload/server/php/files/' . $_POST['filename'] . '.' . $_POST['format'];
	if(file_exists($target_name)||!file_exists($source_name)){
		echo "文件名错误<br />三秒后返回上一页面<script>setTimeout(\"window.history.back(-1)\",3000)</script>";
		exit;
	}else{
		copy($source_name,$target_name);
		unlink($source_name);
	}
	$islocal = 1;
	$url = '';
	$quality = $_POST['quality'];
	$format = $_POST['format'];
}
if($_POST['upmethod'] == '2'){//外链添加
	$islocal = 0;
	$url = $_POST['outerurl'];
	/*if(!!strpos($url,'?')){
		$n = strpos($url,'?');
		$url = substr($url,0,$n);
	}*/
	$quality = '320';
	$format = '';
}
if($_POST['addmethod'] == '1'){//添加歌曲
	$gamedate = $_POST['gamedate'];
	$title = $_POST['title'];
	$vocalist = $_POST['vocalist'];
	$game = $_POST['game'];
	$album = $_POST['album'];
	$date = date("Y-m-d");
	$path = '/' . substr(date("Y"),2) . date("m") . '/';
	$sql = "INSERT INTO music_full (gamedate,title,vocalist,game,album,quality,format,path,date,islocal,url) VALUES ('$gamedate','$title','$vocalist','$game','$album','$quality','$format','$path','$date','$islocal','$url')";
	if($res = mysqli_query($link,$sql)){
		echo "<p>提交成功，新表格数据为：<br />$gamedate|$title|$vocalist|$game|$album|$quality|$url<br />三秒后返回上一页面</p><script>setTimeout(\"window.history.back(-1)\",3000)</script>";
	}
}
if($_POST['addmethod'] == '2'){//修改歌曲
	$mid = $_POST['mid'];
	if($res = mysqli_query($link,"SELECT quality,format FROM music_full WHERE mid = '$mid'")){
		$row = mysqli_fetch_row($res);
		mysqli_free_result($res);
	}
	$oquality = explode("/", $row[0]);
	$oformat = explode("/", $row[1]);
	foreach($oquality as $k => $v){
		$f = $oformat[$k];
		$new[][$f] = $v;
	}
	$format = $_POST['format'];
	$quality = $_POST['quality'];
	$new[][$format] = $quality;
	asort($new);
	foreach($new as $r){
		foreach($r as $k => $v){
			$nquality[] = $v;
			$nformat[] = $k;
		}
	}
	$quality = implode("/", $nquality);
	$format = implode("/", $nformat);
	//echo $quality.$format;exit;
	$sql = "UPDATE music_full SET quality='$quality',format='$format' where mid='$mid'";
	if($res = mysqli_query($link,$sql)){
		echo "<p>提交成功，所修改的表格数据为：<br />$mid|$quality|$format<br />三秒后返回上一页面</p><script>setTimeout(\"window.history.back(-1)\",3000)</script>";
	}
}