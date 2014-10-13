/*search js Antonopoulos Spyridon*/
$(document).ready(function(){
	$("#jumpToPageBox").hide(); 
});

$("#jumpToPage").live("click",(function(){
      $("#jumpToPageBox").toggle();
}));     



function ChangePage(nump,sort_m,sort_w, sa,rpp){
    var url = "./search.php?nump="+nump+"&sort_m="+sort_m+"&sort_w="+sort_w+"&sq="+sa+"&rpp="+rpp;
    window.location=url;
}

