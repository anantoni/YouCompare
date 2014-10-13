var printBox = function(ids){

    var id  =   ids.substring(4,ids.length);
    var input_ = document.getElementById("testinput_xml");
    input_.focus();


    var box = document.getElementById("extraBox");
    // set width perc for td1,td2    

    var hiddiv2 = document.getElementById("hiddenState");
    hiddiv2.innerHTML ="expand";
    
    var anim_time = 1000;
    if (box.innerHTML != "" && box.innerHTML != " "){
        anim_time = 0;
    }
    
     
    // xml output data
    var responseXml = " ";

    var url2 = "./includes/autoSearchCat.php?cid="+id;
    
    var xmlhttp3;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp3 =new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp3.open("GET",url2,false);
    xmlhttp3.send();
    
    responseXml = xmlhttp3.responseXML;
    if($(".emptyRows").length){
        var anim_t = $(".emptyRows").length*30;
        $(".emptyRows").stop().animate({
                    height:"26px"
            },anim_t,function(){


        _b.AutoSuggest.prototype.drawExtra(responseXml,box);
        $("#as_testinput_xml").stop().animate({ 
            width: "900px"   
        }, anim_time,function(){
            if(box.innerHTML == "" || box.innerHTML == " ")
                _b.AutoSuggest.prototype.drawExtra(responseXml,box); 
            //

            var mytable = document.getElementById("extraBoxTable");
            mytable.style.display ="none";
            $("#extraBoxTable").stop().fadeIn(1700);
             box.style.visibility="visible";
        });


        $("#as_table").stop().animate({
           width:"900px"
        },anim_time,function(){ });
        });
    }
    else {
        _b.AutoSuggest.prototype.drawExtra(responseXml,box);
        $("#as_testinput_xml").stop().animate({ 
            width: "900px"   
        }, anim_time,function(){
            if(box.innerHTML == "" || box.innerHTML == " ")
                _b.AutoSuggest.prototype.drawExtra(responseXml,box); 
            //

            var mytable = document.getElementById("extraBoxTable");
            mytable.style.display ="none";
            $("#extraBoxTable").stop().fadeIn(1400);
             box.style.visibility="visible";
        });


        $("#as_table").stop().animate({
           width:"900px"
        },anim_time,function(){ });
    }
    return;
            
    
}
var closeBox =function(){
    // do something
    var fld = document.getElementById("testinput_xml");
    var box = document.getElementById("extraBox");
    var hiddiv2 = document.getElementById("hiddenState");
    hiddiv2.innerHTML ="close";
    var anim_time = 1000;
    var fade_time = 900;

    if (box.innerHTML == "" || box.innerHTML == " "){
        return;
    }

    fld.focus(); // focus again in form
    
    $("#as_testinput_xml").stop().animate({ 
        width: fld.style.width    
    }, anim_time,function(){
  
            box.innerHTML=" ";  
            box.style.visibility="hidden";
            if($(".emptyRows").length){
                var anim_t = $(".emptyRows").length*30;
                $(".emptyRows").stop().animate({
                    height:"0px"
                },anim_t,function(){});  
            }
    });

    $("#as_table").stop().animate({
        width:fld.style.width
    },anim_time,function(){ 
    });
    
    $("#extraBoxTable").stop().fadeOut(fade_time);    
    
};






$(function(){
    $("#testinput_xml").keyup(function(e){
        if(e.keyCode == 38 || e.keyCode == 40) {
            var sug = document.getElementById("as_testinput_xml");
            if(sug){ 
                var hiddiv2 = document.getElementById("hiddenState");
                if(hiddiv2.innerHTML =="expand") {
                    e.preventDefault();
                    var hiddiv = document.getElementById("hiddenId");
                    var place = hiddiv.innerHTML;
                    var doc  = document.getElementById("as_table");
                    var cid = doc.childNodes[place-1].childNodes[2].childNodes[0].childNodes[0].id;
                    printBox(cid);
                }
            }

        }
    });
    
    $("#testinput_xml").bind('keydown', function(e) {     
        if(e.keyCode==37){
            var sug = document.getElementById("as_testinput_xml");
            if(sug){ 
                e.preventDefault();
                closeBox();
            }
        }
        else if(e.keyCode==39){
            var sug = document.getElementById("as_testinput_xml");
            if(sug){ 
                e.preventDefault();
                var hiddiv = document.getElementById("hiddenId");
                var place = hiddiv.innerHTML;
                var doc  = document.getElementById("as_table");
                var cid = doc.childNodes[place-1].childNodes[2].childNodes[0].childNodes[0].id;
                printBox(cid);
            }
        }

        
    });
    
   
});



$(function(){
    // done
    $("#closeImage").live("click",function(){
        closeBox();
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
        printBox(ids);        
    });
        
    
    
});
