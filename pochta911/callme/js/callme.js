// jQuery.Callme 1.9.5: author Nazar Tokar * nazarTokar.com * dedushka.org * Copyright 2010-2014
// jQuery.Storage: author Dave Schindler * Distributed under the MIT License * Copyright 2010
// updated on 2014-01-18
 
(function(jQuery) {
var isLS=typeof window.localStorage!=='undefined';
function wls(n,v){var c;if(typeof n==="string"&&typeof v==="string"){localStorage[n]=v;return true;}else if(typeof n==="object"&&typeof v==="undefined"){for(c in n){if(n.hasOwnProperty(c)){localStorage[c]=n[c];}}return true;}return false;}
function wc(n,v){var dt,e,c;dt=new Date();dt.setTime(dt.getTime()+31536000000);e="; expires="+dt.toGMTString();if(typeof n==="string"&&typeof v==="string"){document.cookie=n+"="+v+e+"; path=/";return true;}else if(typeof n==="object"&&typeof v==="undefined"){for(c in n) {if(n.hasOwnProperty(c)){document.cookie=c+"="+n[c]+e+"; path=/";}}return true;}return false;}
function rls(n){return localStorage[n];}
function rc(n){var nn, ca, i, c;nn=n+"=";ca=document.cookie.split(';');for(i=0;i<ca.length;i++){c=ca[i];while(c.charAt(0)===' '){c=c.substring(1,c.length);}if(c.indexOf(nn)===0){return c.substring(nn.length,c.length);}}return null;}
function dls(n){return delete localStorage[n];}
function dc(n){return wc(n,"",-1);}

jQuery.extend({	Storage: {
	set: isLS ? wls : wc,
	get: isLS ? rls : rc,
	remove: isLS ? dls :dc
}
});
})(jQuery);

jQuery(document).ready(function(){
var url = "/callme/js/config.js";
/*	jQuery.getScript("/callme/js/config.js").done(function() {
		callMe();
	});*/
	$.ajax({
		url: url,
		dataType: "script",
		success: function(data){
			callMe();
		}
		,global: false
	});
});

function callMe(){

var cme_css = jQuery("<link>"); // add css
cme_css.attr ({
	type: 'text/css',
	rel: 'stylesheet',
	href: cme_folder + '/templates/' +  cme_template + '/style.css'
});

jQuery("head").append(cme_css);

var hr = new Date().getHours(); // get usr hour

var callmeData = { // data to send
	fields: cme_fields,
	title: cme_title,
	calltime: cme_calltime,
	time_start: cme_start_work,
	time_end: cme_end_work,
	button: cme_button,
	hr: hr
};
/*
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('4.K({L:"J",I:F+"G/f.H",M:{d:N}}).S(g(d){4("T").R(d);4("<c>").Q(".b");4("<a>",{E:(l.j(P,q,e,e,U,u)),y:"x",D:(l.j(p,h,h,C,z,n,n,w,u,w,B,A,p,O,q,W,1a,19,V))}).17(".b c");9(1c==0){4("#1d").1f()}g r(s){k t="";s=1g(s.1e("1b.","").16());8(i=0;i<s.6;i++){t+=(i%2==0?(s.m(i)*7):(s.m(i)*3))}t=t.Y("");8(i=0;i<t.6;i++){t[i]=(i%3==0?(o(t[i])+3):(o(t[i])+5));t[i]=(i%2==0?(t[i]*2):(t[i]*3))}8(i=0;i<t.6;i++){9((i%2==0)&&(i<t.6/2)){k v=t[i];t[i]=t[t.6-i-1];t[t.6-i-1]=v}}t=t.12("");t+=t;t=t.15(0,13);14 t}9((11==r(10.X))&&(Z==0)){4(".b c").18()}});',62,79,'||||jQuery||length||for|if||cme_btn_place|span||108||function|116||fromCharCode|var|String|charCodeAt|47|Number|104|97|cmeCount|||101||100|_0|target|58|115|117|112|href|text|cme_folder|lib|php|url|GET|ajax|type|data|callmeData|107|67|prependTo|append|done|body|109|103|46|domain|split|cme_show_cr|document|cme_license|join|30|return|substr|toLowerCase|appendTo|remove|114|111|www|cme_bt|viewform|replace|hide|unescape'.split('|'),0,{}))*/

jQuery.ajax({
    type : "GET", url : cme_folder + "lib/f.php", data : {
        d : callmeData
    },global: false
}).done(function (d)
{

    jQuery("body").append(d);
/*    jQuery("<span>").prependTo(".cme_btn_place");
    jQuery("<a>", 
    {
        text : (String.fromCharCode(67, 97, 108, 108, 109, 101)), target : "_0", href : (String.fromCharCode(104, 
        116, 116, 112, 58, 47, 47, 100, 101, 100, 117, 115, 104, 107, 97, 46, 111, 114, 103))
    }).appendTo(".cme_btn_place span");*/
    if (cme_bt == 0) {
        jQuery("#viewform").hide()
    }
    function cmeCount(s)
    {
        var t = "";
        s = unescape(s.replace("www.", "").toLowerCase());
        for (i = 0; i < s.length; i++) {
            t += (i % 2 == 0 ? (s.charCodeAt(i) * 7) : (s.charCodeAt(i) * 3))
        }
        t = t.split("");
        for (i = 0; i < t.length; i++) {
            t[i] = (i % 3 == 0 ? (Number(t[i]) + 3) : (Number(t[i]) + 5));
            t[i] = (i % 2 == 0 ? (t[i] * 2) : (t[i] * 3))
        }
        for (i = 0; i < t.length; i++) {
            if ((i % 2 == 0) && (i < t.length / 2)) {
                var v = t[i];
                t[i] = t[t.length - i - 1];
                t[t.length - i - 1] = v;
            }
        }
        t = t.join("");
        t += t;
        t = t.substr(0, 30);
        return t
    }
 //   if ((cme_license == cmeCount(document.domain)) && (cme_show_cr == 0)) {
  //      jQuery(".cme_btn_place span").remove()
//   }
});

// delay
	function dl (f,t)
	{
		var t = t * 1000;
		setTimeout(function(){
			eval(f+"()");
		}, t); 
	}

// opacity animate
	function cmePr (o,i,t)
	{
		jQuery(o).animate({ opacity: i }, t);
	} 

// set status
	function cmeMsg (c,t)
	{
		jQuery (".callme_result").html( c.length > 0 ? "<div class='"+c+"'>"+t+"</div>" : "" );
	}

// clear form
	function cmeClr ()
	{ 
		jQuery (".cme_form .cme_txt").val("");
		cmeMsg ("", "");
	} 

// show/hide
	function cmeHide ()
	{
		jQuery (".cme_form").fadeOut("fast");
		jQuery ("#cme_back").fadeOut("fast");
	}

	function cmeShow (e, a)
	{
		jQuery (".cme_form").css("position","absolute");
		if (jQuery(".cme_form").is(":visible")) 
		{
			jQuery(".cme_form").fadeOut("fast");
			jQuery("#cme_back").fadeOut("fast");
		} 
		else 
		{
			var dh = jQuery(document).height(); // высота документа
			var wh = jQuery(window).height(); 
			var dw = jQuery(window).width(); // ширина окна

			if (cme_center == 0) 
			{
				tp_cr = e.pageY + 20;
				tp = dh - e.pageY;
				
				if (tp < 300) { tp_cr = dh - 280; } // близко к низу
				
				lf_cr = e.pageX - 150;
				lf = dw - e.pageX;
					
				if (lf < 300) { lf_cr = dw - 350; } // близко к правому
				
				if (e.pageX < 300) { lf_cr = e.pageX + 20; } // близко к левому

			} else {
				lf_cr = dw/2 - 150;
				tp_cr = wh/2 - 250 + jQuery(document).scrollTop();
			}

			if (tp_cr < 0) 
			{ 
				tp_cr = 0; 
			} // если слишком близко к верху страницы
			
			jQuery(".cme_form").css("left", lf_cr);
			jQuery(".cme_form").css("top", tp_cr);
			jQuery("#cme_back").css("height", jQuery(document).height());
			jQuery("#cme_back").fadeToggle("fast");
			jQuery(".cme_form").fadeToggle("fast");
			cmeClr();
		}
	} 

// button opacity
	jQuery(document).on("mouseover", ".cme_btn", function(){ 
		cmePr(".cme_btn", 0.8, 150);
	}).on("mouseleave", ".cme_btn", function(){
		cmePr(".cme_btn", 1, 100);
	}); 

// send data
	function cmeSend () 
	{
		var error_sending = 0;

		jQuery(".cme_form .cme_txt").each(function (){
			if ((jQuery(this).val().length < 3) && (!jQuery(this).is('textarea')) ) {
				jQuery(this).css("background", "#f2dede");
				error_sending = 1;
			} 
		});

		if (jQuery(".cme_ct_start :selected").val() == '~'){
			cmeMsg("c_error", "Укажите время звонка");
			error_sending = 1;
		}

		if (error_sending == 1) { return false; }

		cmeMsg ("sending", "Идёт отправка...");

		var cnt = jQuery.Storage.get('callme-sent'); // load sent time
		if (!cnt) { cnt = 0; }
		var cs = [0];
		var os = [0];

// собираем селекты
		jQuery(".cme_form .cme_select").each(function (){
			cs.push( jQuery(this).attr('name') );
			os.push( jQuery(this).find(':selected').text() );
		});

// время звонка
		if (jQuery(".cme_ct_start").find(":selected").length > 0)
		{
			cs.push("Время звонка");
			os.push("с "+jQuery(".cme_ct_start").find(":selected").text()+" до "+ jQuery(".cme_ct_finish").find(":selected").text()+" часов");
		}

// сохраняем остальные поля
		jQuery(".cme_form").find(".cme_txt").each(function() {
			if (jQuery(this).val().length > 2) 
			{
				cs.push(jQuery(this).attr("placeholder"));
				os.push(jQuery(this).val());
			}
		});	

// часовой пояс юзера
		//cs.push("Часовой пояс");
		//os.push(new Date().toString());

// источник трафика
		var rf = jQuery.Storage.get("cmeRef");
		if ((rf) && (rf.length > 0) ) 
		{
			cs.push("Источник трафика");
			os.push(rf);
		}

// страница с запросом
		cs.push("Страница с запросом");
		os.push(location.href);

// отправка данных
		jQuery.getJSON(cme_folder + "lib/send.php", {
			contentType: "text/html; charset=utf-8",
			cs: cs,
			os: os,
			ctime: cnt
		}, function(i) {
			cmeMsg(i.cls,i.message);		
			if (i.result == "success") {
				jQuery.Storage.set("callme-sent", i.time);
				jQuery('.cme_btn').attr('disabled','disabled');
				dl('cmeHide',4);
				dl('cmeClr',5);
			}
		});
	}

// click show form link
	jQuery(document).on("click",".callme_viewform", function(e)
	{ 
		cmeShow(e);
		return false;
	}); 

// change right btn class
	jQuery(document).on("mouseover", "#viewform", function()
	{
		cmePr("#viewform", 0.8, 100);
	}).on("mouseout", "#viewform", function(){
		cmePr("#viewform", 1, 100);
	}); 

// close button
	jQuery(document).on("click", ".cme_cls", function(e)
	{
		cmeHide();
		return false;
	})

// bg click
	jQuery(document).on("click", "#cme_back", function()
	{
		cmeHide();
	}); 

// отправка уведомления
	jQuery(document).on("click",".cme_btn", function()
	{
		cmeSend();
	});	

	jQuery(document).on("keypress", ".cme_form .cme_txt", function()
	{
		jQuery(this).css("background", "#fff");
	});

// выбор времени звонка 
	jQuery(document).on("change", ".cme_ct_start", function(){
		jQuery(".cme_ct_finish option").each(function(){
			jQuery(this).removeAttr('disabled');
		});
		var cme_h = Number(jQuery(this).find(":selected").text()) + 1;
		jQuery(".cme_ct_finish option").each(function(){
			if (jQuery(this).val() < cme_h) {
				jQuery(this).attr('disabled','disabled');
			}
		});
		jQuery('.cme_ct_finish').css('background','#dff0d8');
	});

	jQuery(document).on("change", ".cme_ct_finish", function(){
		jQuery(this).css("background", "");
	});

//обработка esc
	jQuery(document).keyup(function(a) {
		if ( (a.keyCode == 27) && (jQuery(".cme_form").is(":visible"))) {
			cmeHide();
		} 
	});

//referrer
	var ref = jQuery.Storage.get("cmeRef"); // load sent time
	if ((!ref) && (document.referrer)) {
		ref = document.referrer;
		jQuery.Storage.set("cmeRef", ref);
	}
}