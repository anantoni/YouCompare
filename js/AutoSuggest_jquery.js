/*****************************************************************************\
|        \  ,  /                                                            **|
|     ' ,___/_\___, '           *****         ******                        **|            
|        \ /o.o\ /              *    *          **                          **|
|    -=   > \_/ <   =-          *     *         **                          **|
|        /_\___/_\              *    *          **                          **|
|     . `   \ /   ` .           *****         ******                        **|
|         /  `  \                                                           **|
|_____________________________________________________________________________|
|   Created By : Antonopoulos Spyridon                                      **|
|   contact me : sdi0600048@di.uoa.gr                                       **|
|       Project: YouCompare Site - Software Engineering Course Spring 2011  **|
|   Description: GUI part Jquery animations for AutoSuggest                 **|
|        Lines : 290                                                        **|   
\*****************************************************************************/
var printBox = function(ids,state){

    var id  =   ids.substring(4,ids.length);
    var input_ = document.getElementById("testinput_xml");
    input_.focus();

	
    var box = document.getElementById("extraBox");
    // set width perc for td1,td2    

    
    var anim_time = 600;
    if (box.innerHTML != "" && box.innerHTML != " "){
        anim_time = 0;
    }
    
     
    // xml output data
    var responseXml = " ";
    var url2 = "./ajax/autoSearchCat.php?cid="+id;
    
    var xmlhttp3;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp3 =new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
    }
    var fade_time = 500;
    xmlhttp3.open("GET",url2,false);
    xmlhttp3.send();

    responseXml = xmlhttp3.responseXML;
    var mytable = document.getElementById("as_table");
    var childs = mytable.childNodes.length;
    var real_childs = 12 -(childs - 2);
    
    var last_row_must = (real_childs*28)+5;
    
    if(state == 0) {


    if(childs < 14) {
        
        var anim_time_10 = real_childs*30;
	$("#lastRow").stop().animate({height:last_row_must},anim_time_10,function(){

                _b.AutoSuggest.prototype.drawExtra(responseXml,box); 
                
		 $("#as_testinput_xml").stop().animate({ 
           		 width: "880px"   
       		 }, anim_time,function(){

                     if(box.innerHTML == "" || box.innerHTML == " ")
               		 _b.AutoSuggest.prototype.drawExtra(responseXml,box); 
                         
                       var mytable = document.getElementById("extraBoxTable");
                        mytable.style.display ="none";
                        $("#extraBoxTable").stop().fadeIn(1500);//function(){
                        box.style.visibility="visible";
                        mytable.style.display="table";
                        var hiddiv2 = document.getElementById("hiddenState");
                        hiddiv2.innerHTML ="expand";       
                });


                $("#as_table").stop().animate({
                   width:"880px"
                },anim_time,function(){ });
        });
        
    }
    else {
        _b.AutoSuggest.prototype.drawExtra(responseXml,box);

        $("#as_testinput_xml").stop().animate({ 
            width: "880px"   
        }, anim_time,function(){
        
            if(box.innerHTML == "" || box.innerHTML == " ")
                _b.AutoSuggest.prototype.drawExtra(responseXml,box); 

                var mytable = document.getElementById("extraBoxTable");
                mytable.style.display ="none";
                $("#extraBoxTable").stop().fadeIn(1500);
                box.style.visibility="visible";
                mytable.style.display="table";
                var hiddiv2 = document.getElementById("hiddenState");
                hiddiv2.innerHTML ="expand";

        });


        $("#as_table").stop().animate({
           width:"880px"
        },anim_time,function(){ });
    }
    
    }
    else {
        $("#extraBoxTable").stop().fadeOut(fade_time,function(){
            _b.AutoSuggest.prototype.drawExtra(responseXml,box); 
            var mytable = document.getElementById("extraBoxTable");
                mytable.style.display ="none";
                $("#extraBoxTable").stop().fadeIn(1300);
                box.style.visibility="visible";
                mytable.style.display="table";
                var hiddiv2 = document.getElementById("hiddenState");
                hiddiv2.innerHTML ="expand";
        });  
    
    }
    return;
            
    
}


var closeMyBox =function(){

	
    var hiddiv2 = document.getElementById("hiddenState");
    hiddiv2.innerHTML ="close";
    // do something
    var fld = document.getElementById("testinput_xml");
    fld.focus();
    var box = document.getElementById("extraBox");

    var anim_time = 600;
    var fade_time = 300;

    if (box.innerHTML == "" || box.innerHTML == " "){
        return;
    }

  
    $("#as_testinput_xml").stop().animate({ 
        width: "324px"   
    }, anim_time,function(){
  
            box.innerHTML=" ";  
            box.style.visibility="hidden";
            var mytable = document.getElementById("as_table");
            var childs = mytable.childNodes.length;
            var real_childs = 12 -(childs - 2);
            var anim_time_10 = real_childs*20;
            
            if (childs < 14) {
                $("#lastRow").stop().animate({
                    height:"5px"
                },anim_time_10,function(){});  
            }
    });

    $("#as_table").stop().animate({
        width:"324px"
    },anim_time,function(){ 
    });
    
    $("#extraBoxTable").stop().fadeOut(fade_time);    
    
};






$(function(){
    $("#testinput_xml").keyup(function(e){
        e.preventDefault();
        if(e.keyCode == 38 || e.keyCode == 40) {
            var sug = document.getElementById("as_testinput_xml");
            if(sug){ 
                var hiddiv2 = document.getElementById("hiddenState");
                if(hiddiv2.innerHTML =="expand") {
                    e.preventDefault();
                    var hiddiv = document.getElementById("hiddenId");
                    if(typeof hiddiv == "undefined")
                        return;
                    var place = hiddiv.innerHTML;
                    if(typeof place == "undefined")
                        return;
                    var doc  = document.getElementById("as_table");
                    if(typeof doc == "undefined")
                        return;
                    if(place-1 < 0)
                        return;
                    var cid = doc.childNodes[place-1].childNodes[2].childNodes[0].childNodes[0].id;
                    if(typeof cid == "undefined")
                        return;
                    printBox(cid,1);
                }
            }

        }
    });
    
    $("#testinput_xml").bind('keydown', function(e) {     
        if(e.keyCode==37){
            var sug = document.getElementById("as_testinput_xml");
            if(sug){ 
                e.preventDefault();
                closeMyBox();
            }
        }
        else if(e.keyCode==39){
            var sug = document.getElementById("as_testinput_xml");
            if(sug){ 
                e.preventDefault();
                var hiddiv = document.getElementById("hiddenId");
                if(typeof hiddiv == "undefined")
                    return;
                var place = hiddiv.innerHTML;
                if(typeof place == "undefined")
                        return;
                var doc  = document.getElementById("as_table");
                if(typeof doc == "undefined")
                        return;
                if(place-1 < 0)
                    return;
                var cid = doc.childNodes[place-1].childNodes[2].childNodes[0].childNodes[0].id;
                var hiddiv2 = document.getElementById("hiddenState");
                if(typeof hiddiv2 == "undefined")
                    return;
                if(hiddiv2.innerHTML =="expand")
                    printBox(cid,1);
                else
                    printBox(cid,0);
            }
        }

        
    });
    
   
});



$(function(){
    // done
    $("#closeImage").live("click",function(){
        closeMyBox();
    }); 
    $("#closeImage2").live("click",function(){
        closeMyBox();
    }); 
});


$(function(){
    // done
    $("#extraBox").live("click",function(){
        var input_ = document.getElementById("testinput_xml");
        input_.focus();
    });
    
    $(".expandImage").live("click",function(){
        var ids = $(this).attr("id");
        var hiddiv2 = document.getElementById("hiddenState");
        if(typeof hiddiv2 == "undefined")
            return;
        if(hiddiv2.innerHTML =="expand")
            printBox(ids,1);
        else
            printBox(ids,0);
    });
        
    
    
});
