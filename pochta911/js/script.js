function show_rights(test){
	if (test) {$("table#grouprights tr.more").show();
	} else {$("table#grouprights tr.more").hide(); }
}
function open_dialog(url, title) {
	var round_id = 'dm_'+getRandomInt(1,9999);
	var div_dialog = '<div id="'+round_id+'"></div>';
	bootbox.dialog({message: div_dialog,title: title});
	$.post(url + "ajax-1/" , {},  function(data) {
		$dialog = $('#'+round_id);
        $dialog.html(data);
		ui_add();
        $dialog.find('form').append('<input type="hidden" name="round_id" value="'+round_id+'"/>');
	});	
}

function showThem(id,title){
	opendialog(id, title || 'Вход',350,320);
}

function opendialog(id, title){
	var div_dialog = $('#'+id).html();
	var box = bootbox.dialog({
		  message: div_dialog,
		  title: title
	});
    box.bind('shown.bs.modal', function(){
        add_phone_masks();
    });
	box.bind('hidden.bs.modal', function () {
		var parent_doc = window.parent.document;
		$('#pochta_frame', parent_doc).removeClass('to_top');
	})
}

function open_text(data,title) {
	data = data==''?'Описание скоро будет...':data;
	bootbox.dialog({ message: data, title: title}).find("div.modal-dialog").addClass("largeWidth");	
}



function ui_add(){
	$('input[type="text"],input[type="password"], textarea').addClass('form-control');
	$('select').addClass('form-control dropdown-toggle');
	$('input[type="button"]').addClass('btn btn-default');
	$('input[type="submit"]').addClass('btn btn-success');
	$('table').addClass('table');
	$('.pass_num').maskInput('** *******');
//	$('.pass_num').maskInput('** **********');
	jQuery('.daty').datepicker( {dateFormat: 'dd.mm.yy'});
	$( ".daty_from" ).datepicker({dateFormat: 'dd.mm.yy',onClose: function( selectedDate ) {$( ".daty_to" ).datepicker( "option", "minDate", selectedDate );}});
	$( ".daty_to" ).datepicker({dateFormat: 'dd.mm.yy',onClose: function( selectedDate ) {$( ".daty_from" ).datepicker( "option", "maxDate", selectedDate );}});
//	$("select.multiselect").multiselect();
	$("select").select2({enableFiltering: true});
//	$("select.multi").multiselect({enableFiltering: true});
//	$("select.multi_tur").multiselect({enableFiltering: true});
	//jQuery('.multiselect').multiselect().multiselectfilter();
	//jQuery('.multi').multiselect({multiple:false,selectedList:1}).multiselectfilter();
	//jQuery('.multi_tur').multiselect({multiple:false,selectedList:1}).multiselectfilter();
	$( ".radio" ).buttonset();
	var def_mp = $('.multi_tur').find('option:selected').attr('rel'); 
	$('#id_mp_new').val(def_mp);
	if ($('#locEdit_color').lenght)
		$('#locEdit_color').colorPicker();
	/*$('.multi_tur').bind('multiselectclick', function(event, ui) {
		var rel = $("option:selected", this).attr("rel"); 
		$("#mp_result select").val(rel);

		//$("#mp_result select").multiselect("widget").find(":radio[value='"+rel+"']").each(function() {  this.click();}); 
	});*/
}
function getRandomInt(min, max){
	return Math.floor(Math.random() * (max - min + 1)) + min;
}


function recover_password(){
    bootbox.prompt({
        title: "Введите ваш номер телефона в формате 89991234567:",
        inputType: 'number',
        buttons: {
            confirm: {
                label: 'Отправить пароль',
                className: 'btn-success'
            },
            cancel: {
                label: 'Отмена',
                className: 'btn-danger'
            }
        },
        callback: function (phone) {
            if ( phone != '' && phone != null ){
                $.post("?RecoverPass", {phone:phone},  function(data) {
                    bootbox.alert(data);
                });
            } else if (phone == null) {

			}else{
                bootbox.alert({
                    message: 'Введите ваш номер телефона.',
                    callback: function () {
                        recover_password();
                    }
                });
            }
        }
    });


}



function delete_row(obj) {
    var row = obj.parentNode.parentNode;
    var table =  row.parentNode;
    if ($('tr', table).length <= 1) {
      bootbox.alert ('Эту строку нельзя удалить!');
      return false; //Не удалять последнюю строку с данными
    }else{

      //проверка на кнопку копирования
      var clone_row ='';
      if ($('.clone_row', row).html() != '') {
          clone_row = $('.clone_row', row).html();                
      }
     
      $(row).remove();  
      $('tr', table).each(function( index ) { //смещение индексов
          if (index >= 0) {
        	  var index_num = +index + 1;
              $(this).attr('class', index_num);
              //если кнопку копирования нужно вернуть
              if (clone_row != '' && ($('tr', table).length) == index_num)
                  $('.clone_row', this).html(clone_row);
          }
      });
     }
}
function clone_row(obj) {
  var row = obj.parentNode.parentNode;
  class_id=$(row).attr("class");
  class_id_new = +class_id+1;
  new_el=$(row).clone().insertAfter($(row));
  $(new_el).attr("class",class_id_new);
  $(new_el).find("input").attr('id','');
  $(new_el).find("input.daty_from").attr('class','daty_from form-control');
  $(new_el).find("input.daty_to").attr('class','daty_to form-control');
  $( ".daty_from", new_el).datepicker({dateFormat: 'dd.mm.yy',onClose: function( selectedDate ) {$( ".daty_to", new_el ).datepicker( "option", "minDate", selectedDate );}});
  $( ".daty_to", new_el).datepicker({dateFormat: 'dd.mm.yy',onClose: function( selectedDate ) {$( ".daty_from", new_el ).datepicker( "option", "maxDate", selectedDate );}});
}

/**
 *
 * @param value Значение
 * @param toId Куда записываем
 * @param toType Тип хранилища [input | select | dom DOMElement]
 * @param isSend Установить переменную и сразуже отправить
 */
function buttonSetFilter(filter_id, toValue, toId, toType, host, isSend) {
	// FilterSendRedirect()
	var fNode = document.getElementById(toId);
	if(fNode != undefined) {
		if(toType == 'input') {
			fNode.value = toValue;
		}
		if(toType == 'select') {
			/* value - is eq. SelectedIndex !!! */
			fNode.value = value;
		}
		if(toType == 'dom') {
			fNode.innerHTML = value;
		}

		if(isSend) {
			Filter_fromId = filter_id;
			Filter_host = host;
			FilterKey++;
			FilterSendRedirect();
		}
		return true;
	}
	return false;
}

var FilterWait = false;
var FilterTimer = undefined;

var Filter_host = undefined;
var Filter_fromId = undefined;
var Filter_backID = undefined;
var FilterKey = 0;

function FilterKeyUP() {
	// FilterTimer = setTimeout('');
	FilterKey++;
	FilterTimer = setTimeout('FilterSendByTimer('+FilterKey+')', 500);
}

function FilterSendByTimer(_FilterKey) {
	if(FilterKey == _FilterKey) {
		FilterSend();
	}
}

function sendFilter(_host, _fromId, _backID) {
	Filter_host = _host;
	Filter_fromId = _fromId;
	Filter_backID = _backID;

	FilterKeyUP();
}

function FilterSend() {
	host = Filter_host;
	fromId = Filter_fromId;
	backID = Filter_backID;

	var str = jQuery("#"+fromId).serialize();
	next = false;
	i = 0;
	jQuery(":text").each(function (i) {
		if(jQuery(this).val().length > 2) {
			next = true;
			i++;
		}
	});

	if(!next && i > 0) return false;


	jQuery.ajax({
		type: "POST",
		url: host,
		data: str,
		success: function(data){
			jQuery('#'+backID).html(data);
			jQuery(".datepicker").datepicker(jQuery.extend({showOn: 'button', buttonImage: '/images/calendar.gif', buttonImageOnly: true},jQuery.datepicker.regional['ru']));
            add_data_table($('.new-logist-data-table'));
            filter_table();
		},
		error: function() {
		}
	});

	Filter_host = undefined;
	Filter_fromId = undefined;
	Filter_backID = undefined;

}

function FilterSendRedirect() {
	host = Filter_host;
	fromId = Filter_fromId;
	backID = Filter_backID;

	var str = jQuery("#"+fromId).serialize();
	next = false;
	i = 0;
	jQuery(":text").each(function (i) {
		if(jQuery(this).val().length > 2) {
			next = true;
			i++;
		}
	});

	if(!next && i > 0) return false;

	fForm = document.getElementById(fromId);

	// alert(host + '/' + str);
	if(fForm) {
		fForm.setAttribute('action', host  );
		// fForm.action = host + '/' + str;
		fForm.setAttribute('method', 'post');
		fForm.setAttribute('target', '_blank');
		fForm.submit();
	} else {
		return false;
	}

	Filter_host = undefined;
	Filter_fromId = undefined;
	Filter_backID = undefined;
}



function printBlock(printLink){
	productDesc = $('#'+printLink).html();
	$('body').addClass('printSelected');
	$('body').append('<div class="printSelection">' + productDesc + '</div>');
	window.print();
	window.setTimeout('pageCleaner()', 100); 
	return false;
}
function pageCleaner(){
	$('body').removeClass('printSelected');
	$('.printSelection').remove();
}



function email_submit(){
	var emailer_subj = $('#emailer_subj').val();
	var emailer_mails = $('#emailer_mails').val();
	var emailer_text = CKEDITOR.instances['edit_content'].getData()
	var emailer_yourmail = $('#e_from').val();
	var logs_int = setInterval('email_log_online()', 500);
	$.ajax({
		  type: "POST",
		  url: "/email/send-1/ajax-1/",
		  data: {emailer_subj:emailer_subj,emailer_mails:emailer_mails,emailer_text:emailer_text,emailer_yourmail:emailer_yourmail},
		  global: false,
		  success:function(data){
			clearInterval(logs_int);
			$('#submit_input').css('background-image', 'none');
			$("#submit_result").html(data);	
			$("#reload_button").show();	
		  }
		});
}

function email_log_online(){
	var old_data = $('#submit_input').val();
	
	$.ajax({
		  type: "POST",
		  url: "/email_log.php",
		  global: false,
		  success:function(data){
			  if (data != '' && old_data != data){
					$('#submit_input').val(data);
					$("#submit_button").hide();
					$("#submit_input").show();
			}
		  }
		});
}


var milisec = 0;
var seconds_def = 300;
var seconds = seconds_def;
var url_set = '';
$('#counter_input').val(seconds_def);
function refresh_page(url) {
    url_set = (url_set != '')?url_set:url;
    if (milisec <= 0) {
        milisec = 9;
        seconds -= 1;
    }
    if (seconds <= -1) {
        milisec = 0;
        sendFilter(url_set+'/ajax-1/', 'form_orders', 'orders_table');
        $('#counter_input').val(seconds_def);
        seconds = seconds_def;
    } else
        milisec -= 1;

    $('#counter_input').val(seconds + "." + milisec + 'c.');
    setTimeout("refresh_page()", 100);
}


