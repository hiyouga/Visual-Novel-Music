<?php
header("Content-type;text/html;charset=utf-8");
require_once 'database.php';
if(!empty($_GET['page'])){
	$page = $_GET['page'];
}else{
	$page = 1;
}
if($_GET['sort'] == 'mid'){
	if($res = mysqli_query($link,"SELECT * FROM music_full ORDER BY gamedate DESC")){
		$arr = array();
		while($row = mysqli_fetch_assoc($res)){
			$arr[] = $row;
		}
		mysqli_free_result($res);
		//var_dump($arr);
		echo json_encode($arr);
	}
}
mysqli_close($link);