/**
 *  author:		Timothy Groves - http://www.brandspankingnew.net
 *  edited by : Spyros Antwnopoulos 20/5/2011 +700 lines edited about 300lines 
 *  change ul li to table added preview expandable panel
 */
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
|   Description: GUI part Javascript for autosuggest                        **|
|        Lines : 1200                                                       **|   
\*****************************************************************************/


 if (typeof(bsn) == "undefined")
	_b = bsn = {};


if (typeof(_b.Autosuggest) == "undefined")
	_b.Autosuggest = {};
else
	alert("Autosuggest is already set!");



_b.AutoSuggest = function (id, param)
{  
	// no DOM - give up!
	//
	if (!document.getElementById)
		return 0;
	
	
	// get field via DOM
	this.fld = _b.DOM.gE(id);

	if (!this.fld)
		return 0;
	

	
	// init variables
	//
        this.formId     = id;
	this.sInp 	= "";
	this.nInpC 	= 0;
	this.aSug 	= [];
	this.iHigh 	= 0;
        this.active     = 0;

	
	// parameters object
	//
	this.oP = param ? param : {};
	
	// defaults	
	//
	var k, def = {minchars:1, meth:"get", varname:"input", className:"autosuggest", timeout:5000, delay:350, offsety:-5, shownoresults: false, noresults: "No results!", maxheight: 300, cache: false, maxentries: 25, maxresults: 12};
	for (k in def)
	{
		if (typeof(this.oP[k]) != typeof(def[k]))
			this.oP[k] = def[k];
          
	}
	
	
	// set keyup handler for field
	// and prevent autocomplete from client
	//
	var p = this;
	
	// NOTE: not using addEventListener because UpArrow fired twice in Safari
	//_b.DOM.addEvent( this.fld, 'keyup', function(ev){ return pointer.onKeyPress(ev); } );
	
	this.fld.onkeypress 	= function(ev){return p.onKeyPress(ev);};
	this.fld.onkeyup        = function(ev){return p.onKeyUp(ev);};

	this.fld.setAttribute("autocomplete","off");
};

_b.AutoSuggest.prototype.onKeyPress = function(ev)
{
	
	var key = (window.event) ? window.event.keyCode : ev.keyCode;



	// set responses to keydown events in the field
	// this allows the user to use the arrow keys to scroll through the results
	// ESCAPE clears the list
	// TAB sets the current highlighted value
	//
	var RETURN = 13;
	var ESC = 27;
     
	
	var bubble = 1;

	switch(key)
	{

		case RETURN:
			this.setHighlightedValue();
			bubble = 0;
			break;

		case ESC:
			this.clearSuggestions();
			break;
	}

	return bubble;
};

_b.AutoSuggest.prototype.onKeyUp = function(ev)
{
	var key = (window.event) ? window.event.keyCode : ev.keyCode;
	
        // Arrow keys
	var ARRUP = 38;
        var ARRLE = 39;
	var ARRDN = 40;
        var ARRRI = 37;
	
	var bubble = 1;

	switch(key)
	{

		case ARRUP:
                        this.changeHighlight(key);  
			bubble = 0;
			break;


		case ARRDN:
                       
                        this.changeHighlight(key);
			bubble = 0;
			break;
		
		
		default:
			this.getSuggestions(this.fld.value);
	}

	return bubble;
	

};








_b.AutoSuggest.prototype.getSuggestions = function (val)
{
	
	// if input stays the same, do nothing
	//
	if (val == this.sInp)
		return 0;
	
	var hiddiv2 = document.getElementById("hiddenState");
        if(hiddiv2) {
            hiddiv2.innerHTML="close";
        }

	//_b.DOM.remE(this.idAs);
	
	
	this.sInp = val;
	
	
	// input length is less than the min required to trigger a request
	// do nothing
	//
	if (val.length < this.oP.minchars)
	{
		this.aSug = [];
		this.nInpC = val.length;
		return 0;
	}
	
	
	
	
	var ol = this.nInpC; // old length
	this.nInpC = val.length ? val.length : 0;
	
	
	
	// if caching enabled, and user is typing (ie. length of input is increasing)
	// filter results out of aSuggestions from last request
	//
	var l = this.aSug.length;
	if (this.nInpC > ol && l && l<this.oP.maxentries && this.oP.cache)
	{
		var arr = [];
		for (var i=0;i<l;i++)
		{
			if (this.aSug[i].value.substr(0,val.length).toLowerCase() == val.toLowerCase())
				arr.push( this.aSug[i] );
		}
		this.aSug = arr;
		
		this.createList(this.aSug);
	
		return false;
	}
	else {
            // do new request
	//
		var pointer = this;
		var input = this.sInp;
		clearTimeout(this.ajID);
		this.ajID = setTimeout( function() {pointer.doAjaxRequest(input)}, this.oP.delay );
                //this.ajID = pointer.doAjaxRequest(input);
	}

	return false;
};


_b.AutoSuggest.prototype.doAjaxRequest = function (input)
{
	// check that saved input is still the value of the field
	//
	if (input != this.fld.value)
		return false;
	
	
	var pointer = this;
	
	
	// create ajax request
	//
	if (typeof(this.oP.script) == "function")
		var url = this.oP.script(encodeURIComponent(this.sInp));
	else
		var url = this.oP.script+this.oP.varname+"="+encodeURIComponent(this.sInp);
	
	if (!url)
		return false;
	
	var meth = this.oP.meth;
	var input = this.sInp;
	
	var onSuccessFunc = function (req) {pointer.setSuggestions(req, input)};
	var onErrorFunc = function (status) {
            pointer.clearSuggestions();
        };

	var myAjax = new _b.Ajax();	
	myAjax.makeRequest( url, meth, onSuccessFunc, onErrorFunc );
	
};





_b.AutoSuggest.prototype.setSuggestions = function (req, input)
{
	// if field input no longer matches what was passed to the request
	// don't show the suggestions
	//
	if (input != this.fld.value){	
		return false;
	}
	
	
	this.aSug = [];

        var xml = req.responseXML;
        if(!xml){
            this.clearSuggestions();
            return false;
        }
        if(typeof xml == "undefined"){
            this.clearSuggestions();
            return false;   
        }
        
        
        if(typeof xml.getElementsByTagName('results') == "undefined"){
            this.clearSuggestions();
            return false;   
        }
        
        if(typeof xml.getElementsByTagName('results')[0] == "undefined"){
            this.clearSuggestions();
            return false;   
        }
        
	var results = xml.getElementsByTagName('results')[0].childNodes;
         if(typeof results== "undefined"){
            this.clearSuggestions();
            return false;   
        }
        
	if(results.length == 0){
		this.clearSuggestions();
		return false;
	}
	for (var i=0;i<results.length;i++)
	{
            if (typeof results[i] == "undefined")
                continue;
            
            if (results[i].hasChildNodes())
                this.aSug.push(  {'id':results[i].getAttribute('id'), 'value':results[i].childNodes[0].nodeValue, 'rate':results[i].getAttribute('rating')} );
        }
	
	
	
	this.idAs = "as_"+this.fld.id;

	this.createList(this.aSug);

};


// ean i leksi perasei to orio prosthetoume dummy ena </br> gia apofugi overflow
function formatText(input , len) {
    ret = "";
    if (input.length > len){
        ret = input.substring(0,len)+"</br>"+input.substring(len,input.length);
    }    
    else
        ret = input;
    return ret;
}



_b.AutoSuggest.prototype.drawExtra=function(xml , ele){

    ele.innerHTML ="";
    if(!xml){
        var div = _b.DOM.cE("div",{id:"extraBoxDiv"});
        var table = _b.DOM.cE("table", {id:"extraBoxTable"});
        var tr0 = _b.DOM.cE("tr",{});
        tr0.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img id=\"closeImage2\" src=\"./images/close.png\"></td>";
        var tr1 = _b.DOM.cE("tr", {});
        tr1.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img src=\"./images/stop_cat.png\" alt=\"STOP\"/></td>";
        table.appendChild(tr0);
        table.appendChild(tr1);
        div.appendChild(table);
        ele.appendChild(div);
        return; 
    }
    
    if(typeof xml.getElementsByTagName('results')[0] == "undefined"){
        var div = _b.DOM.cE("div",{id:"extraBoxDiv"});
        var table = _b.DOM.cE("table", {id:"extraBoxTable"});
        var tr0 = _b.DOM.cE("tr",{});
        tr0.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img id=\"closeImage2\" src=\"./images/close.png\"></td>";
        var tr1 = _b.DOM.cE("tr", {});
        tr1.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img src=\"./images/stop_cat.png\" alt=\"STOP\"/></td>";
        table.appendChild(tr0);
        table.appendChild(tr1);
        div.appendChild(table);
        ele.appendChild(div);
        return; 
    }
    var results = xml.getElementsByTagName('results')[0].childNodes;
    if(typeof results == "undefined"){
        var div = _b.DOM.cE("div",{id:"extraBoxDiv"});
        var table = _b.DOM.cE("table", {id:"extraBoxTable"});
        var tr0 = _b.DOM.cE("tr",{});
        tr0.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img id=\"closeImage2\" src=\"./images/close.png\"></td>";
        var tr1 = _b.DOM.cE("tr", {});
        tr1.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img src=\"./images/stop_cat.png\" alt=\"STOP\"/></td>";
        table.appendChild(tr0);
        table.appendChild(tr1);
        div.appendChild(table);
        ele.appendChild(div);
        return; 
    }
    if( typeof results[0] == "undefined" ||
        typeof results[1] == "undefined" ||
        typeof results[2] == "undefined" ||
        typeof results[3] == "undefined" ||
        typeof results[4] == "undefined" ||
        typeof results[5] == "undefined" ||
        typeof results[6] == "undefined" ||
        typeof results[7] == "undefined" ||
        typeof results[8] == "undefined"   ){
            var div = _b.DOM.cE("div",{id:"extraBoxDiv"});
            var table = _b.DOM.cE("table", {id:"extraBoxTable"});
            var tr0 = _b.DOM.cE("tr",{});
            tr0.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img id=\"closeImage2\" src=\"./images/close.png\"></td>";
            var tr1 = _b.DOM.cE("tr", {});
            tr1.innerHTML="<td colspan=\"2\" style=\"text-align:center;vertical-align:top;\"><img src=\"./images/stop_cat.png\" alt=\"STOP\"/></td>";
            table.appendChild(tr0);
            table.appendChild(tr1);
            div.appendChild(table);
            ele.appendChild(div);
            return; 
    }
    
    var id          =   results[0].childNodes[0].nodeValue;
    var name        =   results[2].childNodes[0].nodeValue;
    var photo       =   results[6].childNodes[0].nodeValue;
    var type        =   results[4].childNodes[0].nodeValue;
    var desc        =   results[7].childNodes[0].nodeValue;
    var kws         =   results[3].childNodes[0].nodeValue;
    var nume        =   results[1].childNodes[0].nodeValue;
    var popularity  =   parseFloat(results[5].childNodes[0].nodeValue);
    popularity = popularity.toFixed(2);
    
    
      

    var type_image ="./images/free.png"; 
    var isLockmsg="<span style=\"color:green\">OPEN</span>";
  
     
    if (type == 0) {
        type_image ="./images/locked.png";
        isLockmsg= "<span style=\"color:red\">PRIVATE</span>";
    }
    

    var div = _b.DOM.cE("div",{id:"extraBoxDiv"});
   
    // check all to fit;
    
    var table = _b.DOM.cE("table", {id:"extraBoxTable"});

    var tr1 = _b.DOM.cE("tr", {id:"extraR1"});
    var tr2 = _b.DOM.cE("tr", {id:"extraR2"});
    var tr3 = _b.DOM.cE("tr", {id:"extraR3"});
    var tr4 = _b.DOM.cE("tr", {id:"extraR4"});


    
    var td1 = _b.DOM.cE(  "td", {id:"extraBoxCell_1"});
    td1.innerHTML = "<img id=\"extraBoxImage\" src=\""+photo+"\" alt=\"image\"/>";
    
    var td2 = _b.DOM.cE(  "td", {id:"extraBoxCell_2"});

  
    td2.innerHTML="<ul id=\"listCell2\"><li><img src=\""+type_image+"\" alt=\"image\"/><span style=\"padding-left:10px\">"+isLockmsg+"\
</span><img id=\"closeImage\" src=\"./images/close.png\"></li><li>\n\
<table class=\"AusInlineTb\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td><span class=\"AusTagName\">Name</span></td><td><span class=\"AusTagVal\">"+name+"</span></td></tr><tr><td><span class=\"AusTagName\">Popularity</span></td><td><span class=\"AusTagVal\">"+popularity+"</span></td></tr><tr><td><span class=\"AusTagName\">Number of Entities</span></td><td><span class=\"AusTagVal\">"+nume+"</span></td></tr></tbody></table></li><li><div><a href=\"./browseCategory.php?cat_id="+id+"\"><span id=\"visitCategory\" class=\"AuS_button\" onclick=\"window.location='./browseCategory.php?cat_id="+id+"';\">Browse Category</span></a></div></li></ul>";


    var td3 = _b.DOM.cE(  "td", {id:"extraBoxCell_3"}); 
    td3.innerHTML ="<hr class=\"myhr_2\"/><span class=\"AusTagName\">Description</span><span class=\"AusTagVal\">"+desc+"</span>";
    var td4 = _b.DOM.cE(  "td", {id:"extraBoxCell_4"}); 
    td4.innerHTML ="<hr class=\"myhr_2\"/><span class=\"AusTagName\">Keywords</span><span class=\"AusTagVal\">"+kws+"</span>";
    var td5 = _b.DOM.cE(  "td", {id:"extraBoxCell_5"}); 
    
   
    var output = "<hr class=\"myhr_2\"/><div style=\"padding-top:2px;\"><span class=\"AusTagName\" style=\"margin-right:4px;\">Popular Entities</span>";
    for(var i = 0 ; i < results[8].childNodes.length ;i++) {
        var pname  = results[8].childNodes[i].childNodes[0].childNodes[0].nodeValue;
        var pid    = results[8].childNodes[i].childNodes[1].childNodes[0].nodeValue;

        output += "<span onclick=\"window.location='./browseEntity.php?cat_id="+id+"&ent_id="+pid+"';\" class=\"AusPopName\">"+pname+"</span>";
        //output += pname;
        if (i <results[8].childNodes.length-1 )
            output += ", ";
    }
    if (i == 0)
        output +="<span class=\"AusTagVal\">No Results</span>";
    output +="</div>";
    td5.innerHTML =output;
     

    td3.colSpan=2;
    td4.colSpan=2;
    td5.colSpan=2;


  
    tr1.appendChild(td1);
    tr1.appendChild(td2);
    tr2.appendChild(td3);
    tr3.appendChild(td4);
    tr4.appendChild(td5);

   
    table.appendChild(tr1);
    table.appendChild(tr2);
    table.appendChild(tr3);
    table.appendChild(tr4);
    div.appendChild(table);

    ele.appendChild(div);
    return;
}


function findAbsolutePosition(obj) {
	var curleft =0;
        var curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
	return [curleft,curtop];
//returns an array
}



_b.AutoSuggest.prototype.createList = function(arr)
{
	var pointer = this;
	
	// get rid of old list
	// and clear the list removal timeout
	//
	_b.DOM.remE("myHeader");
	this.killTimeout();
	
	
	// if no results, and shownoresults is false, do nothing
	//
	if (arr.length == 0 && !this.oP.shownoresults)
		return false;
	
	// create wrap div with cursor
        var head_div =_b.DOM.cE("div", {id:"myHeader"});
	// create holding div
	//
        
	var div = _b.DOM.cE("div", {id:this.idAs, className:this.oP.className});	
	
        // create holding div for mousover action
         var fld = document.getElementById("testinput_xml");
         

       
	
	// create and populate table
	//
        var table = _b.DOM.cE("table", {id:"as_table"});
        //!!! to vazw na einai stathero ~ 400px wide
        table.style.width = this.fld.offsetWidth + "px";
        table.style.emptyCells = "show";
        
	// loop throught arr of suggestions
	// creating an tr , td element for each suggestion
	//

        this.active = arr.length;
        var rest = this.oP.maxresults - arr.length;
        
	for (var i=0;i<arr.length;i++)
	{
            // format output with the input enclosed in a EM element
            // (as HTML, not DOM)
            //
            var tr = _b.DOM.cE("tr", {className:"data"});


            var val = arr[i].value;
            if (val.length > 25){

                val = val.substring(0,24);
                val +="..";
            }
            var st = val.toLowerCase().indexOf( this.sInp.toLowerCase() );

            var output = val.substring(0,st) + "<em>" + val.substring(st, st+this.sInp.length) + "</em>" + val.substring(st+this.sInp.length);


            var span  = _b.DOM.cE("span", {}, output, true);

		
            var a 		= _b.DOM.cE("a", {});
            a.appendChild(span);

            a.name = i+1;
            a.ac_id = arr[i].id;
            a.style.width=215+"px";
            a.onclick = function () {window.location="./browseCategory.php?cat_id="+this.ac_id;return false;};
            a.onmouseover = function () {
                pointer.setHighlight(this.name);}
 
            // for col1
            var a1 = _b.DOM.cE("a", {});
            a1.onclick = function () {window.location="./browseCategory.php?cat_id="+this.ac_id;return false;};
            a1.onmouseover = function () {
                pointer.setHighlight(this.name);
            }
                
            var rank_width = 0;

            if (arr[i].rate < 0)
                rank_width = 0;
            else if (arr[i].rate > 10)
                rank_width = 75;
            else{
                rank_width = arr[i].rate*7.5;
            }
               
                
            var out_div = _b.DOM.cE("div", {className:"mainOuterRateAs"});
            var in_div  = _b.DOM.cE("div", {className:"mainInnerRateAs"});
            in_div.style.width=rank_width+"px";
            out_div.appendChild(in_div);
            a1.style.width =75+"px";
            a1.name = i+1;
            a1.ac_id = arr[i].id;
            a1.appendChild(out_div);
                
            // for col3 expand button

            var a3 = _b.DOM.cE("a", {});
            a3.onmouseover = function () {
                pointer.setHighlight(this.name);
            }

            a3.name = i+1;
            a3.ac_id = arr[i].id;
            var myid = "cat_"+arr[i].id;
            var exp_img = _b.DOM.cE("img", {className:"expandImage",id:myid});
            exp_img.src = "./images/expand.png";
            exp_img.alt = "->";
            exp_img.ac_id = arr[i].id;
                
 
            a3.appendChild(exp_img);

            var itd1 = _b.DOM.cE("td",{className:"col_1"},a1);
            //itd1.style.width =190+"px";

            var itd2 = _b.DOM.cE(  "td", {className:"col_2"}, a  );
            itd2.style.overflow="hidden";
            itd2.style.textOverflow="ellipsis";
            itd2.style.whiteSpace="nowrap";
            itd2.style.width = 215+"px";
                
                
            var itd3 = _b.DOM.cE(  "td",{className:"col_3"},a3);
                
    
            tr.appendChild(itd1);
            tr.appendChild(itd2);
            tr.appendChild(itd3);

        /////////////////////////////////////////////////////////////
        
 
            if (i == 0) {
                var td4 = _b.DOM.cE(  "td", {id:"extraBox"} );
                td4.innerHTML ="";
                td4.rowSpan=18;
                td4.style.visibility="hidden";
                tr.appendChild(td4); 

            }
                
            table.appendChild(tr);
         
	}
        if (arr.length > 0) {
            for (i = 0 ; i < 2;i++){

                var itr;
                if(i == 0)
                    itr = _b.DOM.cE("tr", {id:"emptyRow"});
                else{
                    itr = _b.DOM.cE("tr", {id:"lastRow"});  
                }
                
                var ia = _b.DOM.cE("a", {});
                ia.innerHTML=" ";
                var iitd1 = _b.DOM.cE("td",{},ia);
                
                
                var ia2 = _b.DOM.cE("a", {});
                ia2.innerHTML="";
                var iitd2 = _b.DOM.cE("td",{},ia2);
                
                var ia3 = _b.DOM.cE("a", {});
                ia3.innerHTML=" ";
                var iitd3 = _b.DOM.cE("td",{},ia3);
              

                itr.appendChild(iitd1);
                itr.appendChild(iitd2);
                itr.appendChild(iitd3);
                table.appendChild(itr);
            }
        }

	// no results
	//
	/*if (arr.length == 0 && this.oP.shownoresults)
	{
                var tra = _b.DOM.cE("tr", {className:"as_tr"});
		var tda = _b.DOM.cE("td", {className:"as_warning"}, this.oP.noresults  );
		tra.appendChild( tda );
                table.appendChild(tra);
                
	}*/
	
	
	div.appendChild( table );
        


	// get position of target textfield
	// position holding div below it
	// set width of holding div to width of field
	//
	var pos = _b.DOM.getPos(this.fld);
         
	var inp = document.getElementById("testinput_xml");
	head_div.style.left 		=  pos.x + "px";
	head_div.style.top 		= ( pos.y + this.fld.offsetHeight + this.oP.offsety ) + "px";
        //!!! to vazw na einai stathero ~ 400px wide
        
        head_div.style.width 	= this.fld.offsetWidth + "px";
        div.style.width = this.fld.offsetWidth +"px";
	

	
	// set mouseover functions for div
	// when mouse pointer leaves div, set a timeout to remove the list after an interval
	// when mouse enters div, kill the timeout so the list won't be removed
	//
        /*change them*/
	head_div.onmouseover 	= function(){pointer.killTimeout()};
	head_div.onmouseout 	= function(){pointer.resetTimeout()};
        
        
	// add DIV to document
	//

        head_div.appendChild(div);
    
        document.getElementsByTagName("body")[0].appendChild(head_div);
	
	// currently no item is highlighted
	//
	 var prev_hid1 = _b.DOM.gE("hiddenId");
	 var prev_hid2 = _b.DOM.gE("hiddenState");
	 if(typeof prev_hid1 != "undefined"){
	 	 _b.DOM.remE("hiddenId");
        }
	 if(typeof prev_hid2 != "undefined"){
        	_b.DOM.remE("hiddenState");
        }

        var hiddiv = _b.DOM.cE("div", {id:"hiddenId"});
        hiddiv.innerHTML=this.iHigh;
        hiddiv.style.visibility="hidden";
        var hiddiv2 = _b.DOM.cE("div", {id:"hiddenState"});
        hiddiv2.innerHTML="close";
        hiddiv2.style.visibility="hidden";
        
        document.getElementsByTagName("body")[0].appendChild(hiddiv);
        document.getElementsByTagName("body")[0].appendChild(hiddiv2);

        this.iHigh = 0;

	// remove list after an interval
	//

	
	this.toID = setTimeout(function () {pointer.clearSuggestions();}, this.oP.timeout);
};


_b.AutoSuggest.prototype.changeHighlight = function(key)
{	
	var list = _b.DOM.gE("as_table");
	if (!list)
		return false;
	
	var n;

	if (key == 40)
		n = this.iHigh + 1;
	else if (key == 38)
		n = this.iHigh - 1;
        
	
        var max = this.active;
	if (n > max){
		n = 1;
        }
	if (n < 1){
		n = max;//list.childNodes.length;   
        }
	
        var hiddiv = document.getElementById("hiddenId");
        hiddiv.innerHTML=n;
        hiddiv.style.visibility="hidden";
        this.setHighlight(n);
};



_b.AutoSuggest.prototype.setHighlight = function(n)
{
	var list = _b.DOM.gE("as_table");
	if (!list)
		return false;
	
	if (this.iHigh > 0)
		this.clearHighlight();
	
	this.iHigh = Number(n);
        
	
	list.childNodes[this.iHigh-1].childNodes[1].id = "as_highlight";
        
        
        
	this.killTimeout();
};


_b.AutoSuggest.prototype.clearHighlight = function()
{
	var list = _b.DOM.gE("as_table");
	if (!list)
		return false;
	
	if (this.iHigh > 0)
	{
		list.childNodes[this.iHigh-1].childNodes[1].id = "";
		this.iHigh = 0;
	}
};


_b.AutoSuggest.prototype.setHighlightedValue = function (ac_id)
{
	window.location="./browseCategory.php?cat_id="+ac_id;
	/*if (this.iHigh)
	{
		this.sInp = this.fld.value = this.aSug[ this.iHigh-1 ].value;
		
		// move cursor to end of input (safari)
		//
		this.fld.focus();
		if (this.fld.selectionStart)
			this.fld.setSelectionRange(this.sInp.length, this.sInp.length);
		

		this.clearSuggestions();
		
		// pass selected object to callback function, if exists
		//
		//if (typeof(this.oP.callback) == "function")
		//	this.oP.callback( this.aSug[this.iHigh-1] );
	}*/
};

_b.AutoSuggest.prototype.killTimeout = function()
{
	clearTimeout(this.toID);
};

_b.AutoSuggest.prototype.resetTimeout = function()
{
	clearTimeout(this.toID);
	var pointer = this;
	this.toID = setTimeout(function () {pointer.clearSuggestions()}, 1000);
};


_b.AutoSuggest.prototype.clearSuggestions = function ()
{
	
	this.killTimeout();
	var ele = _b.DOM.gE("myHeader");
	if (ele)
	{
		var fade = new _b.Fader(ele,1,0,350,function () {_b.DOM.remE("myHeader")});
	}
        _b.DOM.remE("hiddenId");
        _b.DOM.remE("hiddenState");
     
        

 };










// AJAX PROTOTYPE _____________________________________________


if (typeof(_b.Ajax) == "undefined")
	_b.Ajax = {};



_b.Ajax = function ()
{
	this.req = {};
	this.isIE = false;
};



_b.Ajax.prototype.makeRequest = function (url, meth, onComp, onErr)
{
	
	if (meth != "POST")
		meth = "GET";
	
	this.onComplete = onComp;
	this.onError = onErr;
	
	var pointer = this;
	
	// branch for native XMLHttpRequest object
	if (window.XMLHttpRequest)
	{
		this.req = new XMLHttpRequest();
		this.req.onreadystatechange = function () {pointer.processReqChange()};
		this.req.open("GET", url, true); //
		this.req.send(null);
	// branch for IE/Windows ActiveX version
	}
	else if (window.ActiveXObject)
	{
		this.req = new ActiveXObject("Microsoft.XMLHTTP");
		if (this.req)
		{
			this.req.onreadystatechange = function () {pointer.processReqChange()};
			this.req.open(meth, url, true);
			this.req.send();
		}
	}
};


_b.Ajax.prototype.processReqChange = function()
{
	
	// only if req shows "loaded"
	if (this.req.readyState == 4) {
		// only if "OK"
		if (this.req.status == 200)
		{
			this.onComplete( this.req );
		} else {
			this.onError( this.req.status );
		}
	}
};










// DOM PROTOTYPE _____________________________________________


if (typeof(_b.DOM) == "undefined")
	_b.DOM = {};



/* create element */
_b.DOM.cE = function ( type, attr, cont, html )
{
	var ne = document.createElement( type );
	if (!ne)
		return 0;
		
	for (var a in attr)
		ne[a] = attr[a];
	
	var t = typeof(cont);
	
	if (t == "string" && !html)
		ne.appendChild( document.createTextNode(cont) );
	else if (t == "string" && html)
		ne.innerHTML = cont;
	else if (t == "object")
		ne.appendChild( cont );

	return ne;
        
};



/* get element */
_b.DOM.gE = function ( e )
{
	var t=typeof(e);
	if (t == "undefined")
		return 0;
	else if (t == "string")
	{
		var re = document.getElementById( e );
		if (!re)
			return 0;
		else if (typeof(re.appendChild) != "undefined" )
			return re;
		else
			return 0;
	}
	else if (typeof(e.appendChild) != "undefined")
		return e;
	else
		return 0;
};



/* remove element */
_b.DOM.remE = function ( ele )
{
   
	var e = this.gE(ele);
	
	if (!e)
		return 0;
	else if (e.parentNode.removeChild(e)){
     
	return true;
        }
	else
		return 0;
};

_b.DOM.getPosX =function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

_b.DOM.getPosY = function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

/* get position */
_b.DOM.getPos = function ( e )
{
	var e = this.gE(e);

	var obj = e;

	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	
	var obj = e;
	
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;

	return {x:curleft, y:curtop};
};










// FADER PROTOTYPE _____________________________________________



if (typeof(_b.Fader) == "undefined")
	_b.Fader = {};





_b.Fader = function (ele, from, to, fadetime, callback)
{	
	if (!ele)
		return 0;
	
	this.e = ele;
	
	this.from = from;
	this.to = to;
	
	this.cb = callback;
	
	this.nDur = fadetime;
		
	this.nInt = 50;
	this.nTime = 0;
	
	var p = this;
	this.nID = setInterval(function() {p._fade()}, this.nInt);
};




_b.Fader.prototype._fade = function()
{
	this.nTime += this.nInt;
	
	var ieop = Math.round( this._tween(this.nTime, this.from, this.to, this.nDur) * 100 );
	var op = ieop / 100;
	
	if (this.e.filters) // internet explorer
	{
		try
		{
			this.e.filters.item("DXImageTransform.Microsoft.Alpha").opacity = ieop;
		} catch (e) { 
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			this.e.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity='+ieop+')';
		}
	}
	else // other browsers
	{
		this.e.style.opacity = op;
	}
	
	
	if (this.nTime == this.nDur)
	{
		clearInterval( this.nID );
		if (this.cb != undefined)
			this.cb();
	}
};



_b.Fader.prototype._tween = function(t,b,c,d)
{
	return b + ( (c-b) * (t/d) );
};  
