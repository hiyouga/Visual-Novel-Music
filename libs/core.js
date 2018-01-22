/*JS - by hoshi_hiyouga*/
var GetUrlValue = function(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        try {
            return decodeURIComponent(r[2]);
        } catch (e) {
            return null;
        }
    }
    return null;
}
$(document).ready(function(){
	//var OriginTitle = document.title;
	//var id = GetUrlValue('id');
	$.get("src/list.php?sort=mid", function(data){
		list = JSON.parse(data);
		$.each(list,function(index,item){
			var num = index + 1;
			if(item.islocal == 1){
				var quality = item.quality.split("/");
				var format = item.format.split("/");
				var i = format.length - 1;
				var txt = '<tr><td>'+num+'</td><td>'+item.gamedate+'</td><td title="'+item.mid+'">'+item.title+'</td><td>'+item.vocalist+'</td><td>'+item.game+'</td><td>'+item.album+'</td><td>'+item.quality+'</td>';
				if(i > 0 && getCookie("vip") == 1){
					txt += '<td><button type="button" class="btn btn-default btn-success musicbtn" link="music'+item.path+item.mid+'-'+quality[i]+'.'+format[i]+'">►</button></td></tr>'+"\n";
				}else{
					txt += '<td><button type="button" class="btn btn-default btn-success musicbtn" link="music'+item.path+item.mid+'-'+quality[0]+'.'+format[0]+'">►</button></td></tr>'+"\n";
				}
			}else{		
				var txt = '<tr><td>'+num+'</td><td>'+item.gamedate+'</td><td title="'+item.mid+'">'+item.title+'</td><td>'+item.vocalist+'</td><td>'+item.game+'</td><td>'+item.album+'</td><td>'+item.quality+'</td><td><button type="button" class="btn btn-default btn-success musicbtn" link="music/?socool&url='+encodeURIComponent(item.url)+'">►</button></td></tr>'+"\n";
			}
			$("#list").append(txt);
		});
	});
});
function getCookie(cname){
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++){
		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	}
	return "";
}
$("body").on("click",".musicbtn",function(){
	var audio = $(".audio")[0];
	audio.pause();
	var src = $(this).attr("link");
	console.log('正在播放:' + src);
	$(".audio").attr("src",src);
	audio.play();
});