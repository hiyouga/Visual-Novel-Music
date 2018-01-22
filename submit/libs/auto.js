$("#addmethod").change(function(){
	if($(this).val() == 1){
		$("#add").show();
	}else{
		$("#add").hide();
	}
	if($(this).val() == 2){
		$("#modify").show();
	}else{
		$("#modify").hide();
	}
});
$("#upmethod").change(function(){
	if($(this).val() == 1){
		$("#local").show();
	}else{
		$("#local").hide();
	}
	if($(this).val() == 2){
		$("#outerlink").show();
	}else{
		$("#outerlink").hide();
	}
});
$(document).ready(function(){
	if($("#addmethod").val() == 1){
		$("#add").show();
	}else{
		$("#add").hide();
	}
	if($("#addmethod").val() == 2){
		$("#modify").show();
	}else{
		$("#modify").hide();
	}
	if($("#upmethod").val() == 1){
		$("#local").show();
	}else{
		$("#local").hide();
	}
	if($("#upmethod").val() == 2){
		$("#outerlink").show();
	}else{
		$("#outerlink").hide();
	}
});
function auto(){
	if($("#outerurl").val() == null){
		$("#warning").text('请先填写页面链接');
	}else{
		var url = encodeURIComponent($("#outerurl").val());
		$.get("auto.php?socool&url=" + url, function(data){
			data = JSON.parse(data);
			if(data.info == 'success'){
				$("#title").val(data.name);
				$("#vocalist").val(data.artist);
				$("#album").val(data.album);
			}else{
				$("#warning").text('歌曲信息获取失败');
			}
		});
	}
}