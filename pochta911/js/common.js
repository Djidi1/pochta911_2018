/**
 * Created by Daniil on 11.12.2016.
 */
function delete_div_row(obj) {
    var row = $(obj).parent().parent();
    var panel = $(row).parent();
    $(row).remove();
    var i = 0;
    $(panel).find('.input-group.routes-block').each(function(group){
        i++;
        $(this).attr('rel',i);
        $(this).find('.input-group-addon').text(i);

    });
    $(panel).find('.input-group:last-child .btn-clone').removeAttr('disabled');
}

function clone_div_row(row) {
    var class_id = $(row).attr("rel");
    var class_id_new = +class_id + 1;
    var new_el = $(row).clone().insertAfter($(row));
    $(new_el).attr("rel", class_id_new);
    $(new_el).find('.input-group-addon').text(class_id_new);
    // даем возможность удалиться созданному
    $(new_el).find('.btn-delete').removeAttr('disabled');
    $(new_el).find('.btn-delete').parent().show();
    // автозаполение адреса
    $(new_el).remove('.typeahead');
    // чистим окна ввода
    $(new_el).find('input, select, textarea').not('.to_time_ready, .to_time, .to_time_end').val('');
    var start_time = $(new_el).find('.time-picker.start').get();
    var end_time = $(new_el).find('.time-picker.end').get();
    set_time_period(start_time,end_time);
    autoc_spb_streets();
    google_autocomlete();
    test_time_routes_add();
    //$(new_el).find("input").attr('id', '');
}
function update_time_ready(obj){
    var time_ready = $(obj).val();
    $('.to_time_ready').val(time_ready);
    test_time_routes_add();
}
function update_time_ready_end(obj){
    var time_ready = $(obj).val();
    $('.to_time_ready_end').val(time_ready);
    test_time_routes_add();
}

function set_time_period (start, end) {
    $(start).datetimepicker(timeoptions);
    $(end).datetimepicker(timeoptions);
    $(start).on("dp.change", function (e) {
        $(end).data("DateTimePicker").minDate(e.date);
    });
    $(end).on("dp.change", function (e) {
        $(start).data("DateTimePicker").maxDate(e.date);
    });
    $(start).on("dp.show", function (e) {
        $(end).data("DateTimePicker").minDate(e.date);
    });
    $(end).on("dp.show", function (e) {
        $(start).data("DateTimePicker").maxDate(e.date);
    });
}



var timer_check_user;
function check_user(obj){
    clearTimeout(timer_check_user);
    var elem_type = $(obj).attr('name');
    timer_check_user=setTimeout(function send_req_check_user(){
        var obj = $('input[name='+elem_type+']:visible');
        var user_id = $('#user_id').val();
        var elem_val = $(obj).val();
        $.post("/admin/checkUser-1/", {user_id:user_id,elem_type:elem_type,value:elem_val},  function(data) {
            if (data == 1){
                $(obj).attr('style','border: 1px solid maroon');
                if ($('.alert-div-'+elem_type).length == 0) {
                    var alert_div = $('<div class="alert alert-danger alert-div alert-div-' + elem_type + '">Введите другое значение...</div>');
                    $(obj).parent().append(alert_div);
                }
                $('input[type=submit]').attr("disabled","disabled")
            }else{
                $(obj).attr('style','border: 1px solid darkgreen');
                $('.alert-div-'+elem_type+'').fadeOut().remove();
                if ($('.alert-div').length == 0) {
                    $('input[type=submit]').removeAttr("disabled");
                }
            }

        });
    },500,elem_type);
}


function send_new_courier(){
    var $bootbox_body = $('.bootbox-body');
    var order_id = $bootbox_body.find('input[name=order_id]').val();
    var order_route_id = $bootbox_body.find('input[name=order_route_id]').val();
    // var new_courier = $('.bootbox-body').find('select[name=new_courier]').val();
    var new_courier = $bootbox_body.find('input[name=new_courier]').val();
    var new_car_courier = $bootbox_body.find('select[name=new_car_courier]').val();
    var courier_comment = $bootbox_body.find('textarea[name=courier_comment]').val();
    var order_info_message = $bootbox_body.find('input[name=order_info_message]').val();

    $.post("/orders/chg_courier-1/", {order_id:order_id,order_route_id:order_route_id,new_courier:new_courier,new_car_courier:new_car_courier,courier_comment:courier_comment,order_info_message:order_info_message},  function(data) {
          bootbox.alert(data,location.reload());
        // bootbox.alert(data);
    });

}


function send_new_status(){
    var $bootbox_body = $('.bootbox-body');

    var order_route_id = $bootbox_body.find('input[name=order_route_id]').val();
    var new_status = $bootbox_body.find('select[name=new_status]').val();
    var stat_comment = $bootbox_body.find('textarea[name=comment_status]').val();
    var order_info_message = $bootbox_body.find('input[name=order_info_message]').val();

    $.post("/orders/chg_status-1/", {order_route_id:order_route_id,new_status:new_status,stat_comment:stat_comment,order_info_message:order_info_message},  function(data) {
        bootbox.alert(data,location.reload());
        // bootbox.alert(data);
    });

}

function getCoordsFromYandex(dest_point, obj, qc_geo){
    var result = '';
    $.get('https://geocode-maps.yandex.ru/1.x/?format=json&geocode='+dest_point, function(data){
        var dest_coord = data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos;
        if (typeof dest_coord !== 'undefined'){
            var des_pos = dest_coord.split(' ');
            result = des_pos[1] + ',' + des_pos[0];
            $(obj).attr('coord', result);
            $(obj).parent().find('.coord').val(result);
        } else {
            bad_coord_results(qc_geo);
            console.log(data);
        }
    }).fail(function(data) {
        bad_coord_results(qc_geo);
        console.log(data.status);
        // 429 - Превышен лимит в 25 000 запросов в сутки
        if (data.status === 429) {
            calc_route(recalc_cost, '')
        }
    });
}

function bad_coord_results(qc_geo) {
    if (qc_geo == 5){
        alert_note('Координаты не определены. Попробуйте указать ближайший адрес.');
    } else if (qc_geo == 4){
        alert_note('Координаты определены с точностью до города. Если необходим более точный расчет попробуйте указать ближайший адрес.');
    } else if (qc_geo == 3){
        alert_note('Координаты определены с точностью до населенного пункта. Если необходим более точный расчет попробуйте указать ближайший адрес.');
    }
}

function autoc_spb_streets(){
    $(".spb-streets").each(function() {
        var $this = $(this);
        $this.suggestions({
            token: "f9654536b2875678e438a578f979e19cc91dbe4b",
            type: "ADDRESS",
            bounds: "city-house",
            count: 5,
            geoLocation: [{"kladr_id": "78"}],
            constraints: [{
                label: "",
                locations: [{region: "Санкт-Петербург"}],
                deletable: false
            }, {
                label: "",
                locations: [{kladr_id: '47'}],
                deletable: false
            }],
            /* Вызывается, когда пользователь выбирает одну из подсказок */
            onSelect: function (suggestion) {
                if (suggestion.data.qc_geo >= 3) {
                    // Если не точный адрес, то пробуем поискать в Яндексе
                    getCoordsFromYandex(suggestion.value, this, suggestion.data.qc_geo);
                }
                $(this).attr('coord', suggestion.data.geo_lat + ',' + suggestion.data.geo_lon);
                $(this).parent().find('.coord').val(suggestion.data.geo_lat + ',' + suggestion.data.geo_lon);
            }
        });
    });
    /*
    // Применяем для подбора улиц
    $(".spb-streets").each(function() {
        var $this = $(this);
        $this.typeahead({
            source: function (query, process) {
                // var textVal=$("#field1").val();
                $.ajax({
                    url: '/service/kladr.php',
                    type: 'POST',
                    data: 'type=street&street=' + query + '&city=',
                    dataType: 'JSON',
                    async: true,
                    timeout: 5000,
                    success: function (data) {
                        process(data);
                    }
                });
            },
            updater: function (item) {
                $($this).parent().parent().find('.to_house').attr('AOGUID', item.id).val('');
                $($this).parent().parent().find('.to_AOGUID').val(item.id);
                $($this).parent().parent().find('.to_region').val(item.region);
                $($this).attr('region', item.region);
                getHouseNumbers();
                return item;
            },
            minLength: 4,
//		scrollHeight: 200,
            items: 'all'
        });
    });
    getHouseNumbers();
    */
    /*
    var saved_data = localStorage.getItem('spb_street_data');
    if (typeof saved_data == 'undefined' || saved_data == null || saved_data == '' ) {
        $.getJSON('/orders/get_data-spbStreets', function(spb_street_data){
            localStorage.setItem('spb_street_data', JSON.stringify(spb_street_data));
            $(".spb-streets").typeahead({ source: spb_street_data, hint: true });
        },'json');
    }else{
        var localData = JSON.parse(localStorage.getItem('spb_street_data'));
        $(".spb-streets").typeahead({ source: localData, hint: true });
    }
    */
}

function getHouseNumbers() {
    // Применяем для подбора домов
    $(".to_house").each(function() {
        var $this = $(this);
        var AOGUID=$($this).attr('AOGUID');
        $this.select2({
            language: "ru",
            ajax: {
                url: "/service/kladr.php",
                type: 'POST',
                data: function (params) {
                    var house = (typeof params.term == 'undefined')?$this.val():params.term;
                    return {
                        house: house, // search term
                        type: 'house',
                        AOGUID: AOGUID
                    };
                },
                dataType: 'JSON',
                async: true,
                timeout: 5000,
                cache: true,
                processResults: function (data) {
                    return {
                        results: $.map(data.items, function (item) {
                            return {
                                text: item.name,
                                id: item.name
                            }
                        })
                    };
                }
            }
        });
        /*
        $.ajax({
            url: '/service/kladr.php',
            type: 'POST',
            data: 'type=house&house=&AOGUID=' + AOGUID,
            dataType: 'JSON',
            async: true,
            timeout: 5000,
            success: function (data) {
                $this.html('');
                $.each(data, function (idx, obj) {
                    $this.append('<option value="' + obj.name + '">' + obj.name + '</option>');
                });
                $this.trigger("liszt:updated");
                $this.select2().css('width','100%');
            }
        });
        */
        /*
         $this.typeahead({
         source: function (query, process) {
         $.ajax({
         url: '/service/kladr.php',
         type: 'POST',
         data: 'type=house&house=' + query + '&AOGUID=' + AOGUID,
         dataType: 'JSON',
         async: true,
         timeout: 5000,
         success: function (data) {
         source_data = data;
         process(data);
         }
         });
         },
         minLength: 0,
         items: 'all'
         }).blur(function () {
         console.log(source_data);
         if(source_data.includes($(this).val())) {
         console.log('Error : element not in list!');
         }
         });
         */
    });
}

function updUserStores(obj){
    var user_id = $(obj).val();
    $.post("/orders/get_data-userStores/", {user_id:user_id},  function(data) {
        data = JSON.parse(data);

        if($('#checkbox_hand_write').prop('checked')) {
            $('#checkbox_hand_write').click();
        }

        $('SELECT.pay_type').val(data.pay_type);
        $('#user_peds_price').val(data.peds_price);
        $('#user_fix_price').val(data.fixprice);
        $('#user_max_price').val(data.maxprice);
        $('.js-store_address').html(data.opts).change();
    });
}

function hand_write(obj) {
    $(obj).closest('form').find('.hand_write').toggle();
    $(obj).closest('form').find('.store_address').toggle();
    $(obj).closest('form').find('.store_address').val(0);
    $(obj).closest('form').find('.store_address').parent().find('.order-add-title').toggle();
    var $sender = $('select[name=new_user_id] option:selected');
    var phone = $sender.attr('phone');
    var sender = $sender.attr('sender');
    var from = $sender.attr('from');
    var from_appart = $sender.attr('from_appart');
    var from_comment = $sender.attr('from_comment');
    $(obj).closest('form').find('input[name="from_phone[]"]').val(phone);
    $(obj).closest('form').find('input[name="from_fio[]"]').val(sender);
    $(obj).closest('form').find('input[name="from[]"]').val(from);
    $(obj).closest('form').find('input[name="from_appart[]"]').val(from_appart);
    $(obj).closest('form').find('input[name="from_comment[]"]').val(from_comment);

    $(obj).closest('form').find('input[name="from[]"]').click();
    $(obj).closest('form').find('input[name="from[]"]').focus();
}


function filter_table(){
    $('TABLE.new-logist-data-table TBODY tr').hide();
    var i = 0;
    $('input.statuses').each(function() {
        if ( $(this).is(':visible') && $(this).prop('checked') ) {
            var chk_val = $(this).val();
            $('TABLE.new-logist-data-table TBODY tr.status_'+chk_val).show();
            // Если статус выбран, но таких строк нет, то показывать все.
            // if ($('.new-logist-data-table tr.status_'+chk_val).length){
                i++;
            // }
        }
    });
    if (i == 0){
        $('.new-logist-data-table TBODY tr').show();
    }
}

function re_calc(obj){
    var route_row = $(obj).parent().parent();
    var cost_tovar = $(route_row).find('.cost_tovar').val();
    var cost_route = $(route_row).find('.cost_route').val();
    var pay_type = $(route_row).find('.pay_type').val();
    var inkass = 0;

    // Скрываем способы оплаты, если зашел клиент
    if ($('BODY').attr('group_id') == 2) {
        $(route_row).find('.pay_type option').hide();

        var PayTypeIsDisabled = $(route_row).find('.pay_type').is(':disabled');
        //Если инкассация больше 0 то автоматически ставиться "Вычитается из полученных"
        if (cost_tovar > 0 && !PayTypeIsDisabled) {
            $(route_row).find('.pay_type').val('3');
            $(route_row).find('.pay_type option[value=3]').show();
        }
        //если человек ставит 0 в инкасацию, то у него остается только 2 варианта – отправитель или получатель выбрать.
        if (cost_tovar == 0 && (pay_type != 1 || pay_type != 2) && !PayTypeIsDisabled) {
            pay_type = (pay_type != 1 && pay_type != 2) ? 1 : pay_type;
            $(route_row).find('.pay_type').val(pay_type);
            $(route_row).find('.pay_type option[value=1]').show();
            $(route_row).find('.pay_type option[value=2]').show();
        }
        if (PayTypeIsDisabled) {
            $(route_row).find('.pay_type option[value=' + pay_type + ']').show();
        }
    }
    if (pay_type == 2){
        inkass = Number(cost_tovar)+Number(cost_route);
    }else{
        inkass = Number(cost_tovar);
    }
    $(route_row).find('.cost_all').val(inkass);
}



//
function time_routes_set(obj){
    var time_target = $(obj).val();
    var route_block = $(obj).parent().parent().get();
    $(route_block).find('.to_time').val(time_target);
    $(route_block).find('.to_time_end').val(time_target);
}

function target_time_show(){
    if ($('.target').prop('checked')){
        $('.target_select').show();
        $('.period_select').hide();
    }else{
        $('.target_select').hide();
        $('.period_select').show();
    }
    test_time_routes_add();
}

function round5(x){
    return Math.ceil(x/5)*5;
}
function round10(x){
    return ('0' + Math.ceil(x/10)*10).slice(-2);
}
function round00(x){
    return Math.ceil(x*100)/100;
}
// адская проверка времени
function test_time_routes_add() {
    var time_edited = $('#time_edited').val();
    var order_id = $('#order_id').val();
    // Запускаем проверку только, если это новый заказ или если в нем было изменено время
    if (order_id == 0 || time_edited == 1) {
        $('.to_time').removeAttr('disabled');
        $('div.routes-block').each(function (index) {
            var next_route = $('div.routes-block').eq(index + 1);
            var this_ready = $(this).find('.to_time_ready').val();
            var this_ready_end = $(this).find('.to_time_ready_end').val();
            var this_to_time = $(this).find('.to_time').val();
            var this_to_time_target = $(this).find('.to_time_target').val();
            var next_to_time = $(next_route).find('.to_time').val();
            var this_to_time_end = $(this).find('.to_time_end').val();
            // var next_to_time_end = $(next_route).find('.to_time_end').val();
            // Текущее время
            var m = moment(new Date());
            var time_now_string = m.hour() + ':' + round10(m.minute());

            // Если текущее время меньше времени готовности
            if (TimeToFloat(this_ready) < TimeToFloat(time_now_string) && moment($('input[name=date]').val(), 'DD.MM.YYYY').isSame(Date.now(), 'day')) {
                $(this).find('.to_time_ready').val(time_now_string);
                alert_note('Время готовности не может быть меньше текущего времени.');
            }
            // Если время готовности ПО меньше времени готовности С
            else if (this_ready_end != '-' && TimeToFloat(this_ready_end) < TimeToFloat(this_ready)) {
                $(this).find('.to_time_ready_end').val(this_ready);
                test_time_routes_add();
            }
            // Если время готовности меньше времени С
            else if ((TimeToFloat(this_ready) > TimeToFloat(this_to_time) ||
                    TimeToFloat(this_ready) > TimeToFloat(this_to_time_target))) {
                $(this).find('.to_time').val(this_ready);
                $(this).find('.to_time_target').val(this_ready);
                test_time_routes_add();
            }
            // Если время С меньше времени ПО
            else if (TimeToFloat(this_to_time) > TimeToFloat(this_to_time_end)) {
                $(this).find('.to_time_end').val(this_to_time);
                test_time_routes_add();
            }

            if (typeof next_to_time != 'undefined') {
                // а. времня начало доставки следующего адреса, меньше или равно времени окончания доставки предыдущего.
                if (TimeToFloat(next_to_time) > TimeToFloat(this_to_time_end)) {
                    $(next_route).find('.to_time').val(this_to_time_end);
                    // блокируем возможность выбор другого времени
                    // disable_next($(next_route).find('.to_time'), this_to_time_end);
                }
                // б. Если от начала доставки первого адреса до конца доставки первого адреса более 60 минут , то программа внутри у себя подставляет что там промежуток в 60 минут, например (с 14,00 до 18,00 , программа в уме держит что там с 14,00 до 15,00)

                // в. время начала следующего заказа , больше или равно времени начало предыдущего адреса но если больше то не более чем на 30 минут.
                if ((TimeToFloat(next_to_time) < TimeToFloat(this_to_time))
                    || round00((TimeToFloat(next_to_time) - TimeToFloat(this_to_time)) > 0.5)) {
                    $(next_route).find('.to_time').val(this_to_time);
                }
                // console.log('this_to_time:' + this_to_time);
                // console.log('this_to_time_end:' + this_to_time_end);
                // console.log('next_to_time:' + next_to_time);
                // console.log('next_to_time_end:' + next_to_time_end);
            }
        });
    }
}

function test_time_routes(obj){
    var route_row = $(obj).parent().parent();
    test_time_routes_each(route_row);
}
function test_time_all_routes(){
    var order_edited = $('#order_edited').val();
    var time_edited = $('#time_edited').val();
    var order_id = $('#order_id').val();

    // Запускаем проверку только, если это новый заказ или если в нем было что-то изменено (кроме примечания)
    // Или изменено только время
    // if ((order_edited == 1 || order_id == 0) && time_edited == 1) {
    if (time_edited == 1 || order_id == 0) {
        // Проверка времени готовности
        var first_time = '';
        var prev_time = '';
        var need_sync = false;
        var $routes_block = $('div.routes-block');
        $routes_block.each(function (index) {
            var this_time = $(this).find('.to_time_ready').val();
            if (index == 0) {
                first_time = this_time;
            }
            if (index != 0 && this_time != prev_time) {
                need_sync = true;
            } else {
                prev_time = this_time;
            }
        });

        var invalid = false;
        $routes_block.find("select:required, input:required", this).each(function () {
            if ($(this).val().trim() == "") {
                invalid = true;
                $(this).focus();
            }
        });
        if (invalid) {
            alert_note('Заполните, пожалуйста, все обязательные поля формы заказа.');
            return false;
        }
        if (need_sync) {
            alert_note('Время готовности всех заказов должно быть единым.<br/>Мы установили время равным времени готовности первого заказа по маршруту');
            $routes_block.find('.to_time_ready').val(first_time);
            return false;
        }

        $routes_block.each(function () {
            var this_ready = $(this).find('.to_time_ready').val();
            var this_ready_end = $(this).find('.to_time_ready_end').val();
            var this_to_time = $(this).find('.to_time').val();
            var this_to_time_end = $(this).find('.to_time_end').val();

            // Время начала доставки не может быть позже времени окончания доставки
            if (this_to_time > this_to_time_end) {
                $(this).find('.to_time').val(this_to_time_end);
                this_to_time = this_to_time_end;
                alert_note('Время начала доставки не может быть позже времени окончания доставки.');
                return false;
            }
            // Время готовности не может быть больше времени доставки
            if (this_ready > this_to_time) {
                $(this).find('.to_time_ready').val(this_to_time);
                alert_note('Время готовности не может быть больше времени начала доставки.');
                return false;
            }
        });
        $routes_block.each(function () {
            test_time_routes_each(this);
        });
    }else{
        document.getElementById("order_edit").submit();
    }
}

function test_time_routes_each(route_row){
    var to_time_ready = $(route_row).find('.to_time_ready').val();
    var to_time_ready_end = $(route_row).find('.to_time_ready_end').val();
    var to_time = $(route_row).find('.to_time').val();
    var to_time_end = $(route_row).find('.to_time_end').val();
/*
     iLog('to_time_ready: '+TimeToFloat(to_time_ready));
     // iLog('to_time: '+TimeToFloat(to_time));
     iLog('to_time_end: '+TimeToFloat(to_time_end));
     iLog('time_now: '+TimeToFloat(timestampToTime()));
*/
    var tt_ready = TimeToFloat(to_time_ready);
    var tt_ready_end = TimeToFloat(to_time_ready_end);
    var tt = TimeToFloat(to_time);
    var tt_end = TimeToFloat(to_time_end);
    var t_now = TimeToFloat(timestampToTime());

    var today = $('.today-date').val();
    var tomarrow = moment(today, 'DD.MM.YYYY').add(1, 'days').format('L');
    var $date = $('input[name=date]');
    var set_date = $date.val();

    // времена для проверки
    var period_tomarrow_from = $('#period_tomarrow_from').val();
    var period_tomarrow_to = $('#period_tomarrow_to').val();
    var period_today_from = $('#period_today_from').val();
    var period_today_to = $('#period_today_to').val();
    var ready_1_from = $('#ready_1_from').val();
    var ready_1_to = $('#ready_1_to').val();
    var ready_1_period = $('#ready_1_period').val();
    var ready_2_period = $('#ready_2_period').val();
    var ready_3_period = $('#ready_3_period').val();
    var ready_today_period = $('#ready_today_period').val();
    var period_period = $('#period_period').val();
    var period_from_period = $('#period_from_period').val();
    var target_time = $('.target').prop('checked');


    // Если время доставки меньше текущего, то заказ на следующий день (проверяю по второму времени)
    var tt_end_2 = round00(tt_end - t_now) < 0 ? tt_end + 24 : tt_end;
    var tt_2 = round00(tt_end - t_now) < 0 ? tt + 24 : tt;

    var no_error = true;
    var errors = '<ul>';
    // Если время доставки меньше готовности, то заказ на следующий день
    tt_end = round00(tt_end - tt_ready) < 0 ? tt_end + 24 : tt_end;

    // (0) Время между "забрать по" и "доставить с" не может быть больше 120 минут (2х часов), если стоит бесконечность по умолчанию то эта проверка не нужна
    if (round00(tt - tt_ready_end) > ready_3_period && to_time_ready_end != '-') {
        errors += '<li>Время начала доставки не может быть больше '+ready_3_period+':00 от времени "забрать по".</li><br/>';
        no_error = false;
    }

    // (1) Заказы вечером на завтра запрещены на утро (проверка по крайнему времени доставки)
    if ( set_date == tomarrow && t_now > period_tomarrow_to && tt_end < period_tomarrow_from ){
        errors += '<li>Заказ с доставкой в период с 8:00 до '+period_tomarrow_from+':00 можно оставить не позднее '+period_tomarrow_to+':00.</li><br/>';
        no_error = false;
    }
    // (2) Заказы утром на сегодняшнее утро запрещены  (проверка по крайнему времени доставки)
    if ( set_date == today && t_now < period_today_to && tt_end < period_today_from ){
        errors += '<li>Заказ с доставкой в период с 8:00 до '+period_today_from+':00 можно оставить не раньше '+period_today_to+':00.</li><br/>';
        no_error = false;
    }

    // (3) если готовность букета с 8 до 10, то мы разрешаем выставлять рамки заказа в пределах 1 часа от времени готовности,
    // и остальные проверки кроме, 2,5 часов оствляем
    if (tt_ready >= ready_1_from && tt_ready <= ready_1_to){
        // проверка от готовности до начала доставки - 2 час
        if (round00(tt_end - tt_ready) < ready_1_period){
            errors += '<li>Значение "Доставить по" не может быть менее '+ready_1_period+' ч. от значения "Можно забрать с".</li><br/>';
            no_error = false;
        }
    } else {
        // (4) проверка от готовности (2,5 часа) - 18.05.2017 - заменил на 3 часа
        if (round00(tt_end - tt_ready) < ready_2_period) {
            errors += '<li>Значение "Доставить по" не может быть менее '+ready_2_period+' ч. от значения "Можно забрать с".</li><br/>';
            no_error = false;
        }
        // (5) Проверка от времени заказа
        if (set_date == today && round00(tt_end_2 - t_now) < ready_today_period ){
            errors += '<li>Значение "Доставить по" не может быть менее '+ready_today_period+' ч. от текущего времени.</li><br/>';
            no_error = false;
        }
    }
    // интервал готовности не меньше указанного
	
    if ((tt_ready_end - tt_ready) < (period_from_period / 60) && to_time_ready_end != '-'){
            errors += '<li>Интервал готовности должен быть не меньше '+period_from_period+' мин.</li><br/>';
            no_error = false;
    }
    // (6) Проверка от и до не менее 40 мин, если только не к точному времени
    if (round00(tt_end_2 - tt_2) < (period_period / 60) && !target_time){
        errors += '<li>Интервал между значениями "Доставить с" и "Доставить по" не может быть менее '+period_period+' мин.</li><br/>';
        no_error = false;
    }



    // Только для новых заказов
    if ($('#order_id').val() == '') {
        // Добавляем день, если заказ на текущей и время готовности меньше текушего
        if (moment($date.val(), 'DD.MM.YYYY').isSame(Date.now(), 'day') && (tt_end - t_now) < -1) {
            $date.val(moment($date.val(), 'DD.MM.YYYY').add(1, 'days').format('L'));
        }
    }
/*
    // проверка обязательный полей
    var fail = false;
    var fail_log = '';
    $( '#form_id' ).find( 'select, textarea, input' ).each(function(){
        if( ! $( this ).prop( 'required' )){

        } else {
            if ( ! $( this ).val() ) {
                fail = true;
                var name = $( this ).attr( 'title' );
                fail_log += "Поле '" + name + "' должно быть заполнено. \n";
            }

        }
    });
*/

    // Группа пользователя
    var group_id = $('BODY').attr('group_id');

    // Если клиент, то пусть исправляет
    if (group_id == 2) {
        // Блокировка при ошибках во времени
        if (!no_error) {
            $('input.btn-submit').prop('disabled', true);
            bootbox.alert(errors + '</ul><br/>Откорректируйте, пожалуйста, временные рамки.', function () {
                $('input.btn-submit').prop('disabled', false);
            });
        } else {
            $('input.btn-submit').prop('disabled', false);
        }
    }else{
        // Запрос при ошибках во времени
        if (!no_error) {
            $('input.btn-submit').prop('disabled', true);
            var dialog = bootbox.confirm({
                message: errors + '</ul><br/>Продолжить с указанными параметрами?',
                buttons: {
                    confirm: {
                        label: 'Сохранить заказ',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'Отмена',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    $('input.btn-submit').prop('disabled', false);
                    dialog.modal('hide');
                    if (result) {
                        document.getElementById("order_edit").submit()
                    }
                    return result;
                }
            });
        } else {
            $('input.btn-submit').prop('disabled', false);
        }
    }
/*
    // Блокировка по обязательным полям
    if ( fail ) {
        $('input.btn-submit').prop('disabled', true);
        bootbox.alert( fail_log, function(){ $('input.btn-submit').prop('disabled', false); } );
        // Если нет других ошибок, то блокируем данной проверкой.
        no_error = (no_error)?false:fail;
    }
*/

    if (no_error) {
        document.getElementById("order_edit").submit()
    }else{
        return no_error;
    }
}

/**
 * @return {number}
 */
function TimeToFloat(time){
    var result = 0;
    if (time != '' && time != null){
        var to_time_ready_arr = time.split(':');
        result = parseInt(to_time_ready_arr[0])+parseInt(to_time_ready_arr[1])/60;
    }
    return round00(result);
}

function timestampToTime(){
    var unix_timestamp = $('#time_now').val();
    $('span.his_time_now').html(moment.unix(unix_timestamp).format("HH:mm:ss"));
    return moment.unix(unix_timestamp).format("HH:mm");
}

function add_data_table(obj, type){
    /*
    // Setup - add a text input to each footer cell

    $(obj).find('tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
*/
    var no_data = 'В таблице отсутствуют данные';
    if (type == 'logist') {
        no_data = 'Нет заказов на выбранную дату';
    }
    if (type == 'client') {
        no_data = 'У Вас нет заказов на выбранную дату';
    }
    // DataTable
    $(obj).DataTable({
        "language": {
            "processing": "Подождите...",
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": no_data,
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }
        }
        , "bLengthChange": false
        , "bPaginate": false
        , "bFilter": true
        , "stateSave": true
        , "order": [[ 2, 'asc' ]]
    });
}


function firstToUpperCase( str ) {
    return str.substr(0, 1).toUpperCase() + str.substr(1);
}

function google_autocomlete(){
    if ($('.address').length) {
        $('input.address').each(function () {
            var input = $(this).get(0);
            var options = {
                componentRestrictions: {country: 'ru'}
            };
            new google.maps.places.Autocomplete(input, options);
        });
    }
}

function open_bootbox_dialog(url) {
    window.location.href = url;
}

//Блокировка времени после значения
function disable_next(obj, value){
    var disable_next = false;
    $(obj).find('option').each(function () {
        if (disable_next){
            $(this).attr('disabled','');
        }
        if ($(this).text() == value){
            disable_next = true;
        }
    });
}

function chg_status(order_id){
    $.post("/orders/chg_status-1/", {order_id:order_id},  function(data) {
        bootbox.confirm({
            title: "Изменение статуса доставки в заказе № "+order_id,
            message: data,
            callback: function(result){ if(result){send_new_status()} }
        });
        // bootbox.alert(data,send_new_status(this));
    });
}

function cancel_order(order_id){
    bootbox.confirm({
        title: "Отмена заказа № "+order_id,
        message: "Вы уверены, что хотите отменить заказ № "+order_id+"?",
        callback: function(result){ if(result){
            $.post("/orders/chg_status-1/",
                {order_id:order_id,order_route_id:'',new_status:5,stat_comment:'отмена заказа',order_info_message:' '},
                function(data) {
                    bootbox.alert(data,location.reload());
                });
        } }
    });

}

function popup_excel(url) {
    bootbox.alert({
        title: "Экспорт в эксель",
        message: '<iframe style="border:0;" src="/'+url+'/without_menu-1/" height="200" width="100%"  scrolling="no"></iframe>',
        className: "minWidth",
        buttons: {
            'ok': {
                label: 'Закрыть',
                className: 'btn-default pull-left'
            }
        }
    });
}

function chg_courier(order_id){
    // var order_route_id = $('tr.order_'+order_id).first().attr('rel');
    $.post("/orders/chg_courier-1/", {order_id:order_id},  function(data) {
        bootbox.confirm({
            title: "Изменение курьера в заказе № "+order_id,
            message: data,
            callback: function(result){ if(result){send_new_courier()} }
        });
        //bootbox.alert(data,send_new_status(this));
    });
}

function focus_field(field, dont_style){
    if (dont_style !== 1) {
        $(field).attr('style', 'border:2px solid red;');
        $(field).change(function () {
            $(this).attr('style', '');
        });
    }
    $(field).focus();
}

function add_phone_masks() {
    $('.phone-number').mask('+7 999 999-99-99',{placeholder:"+7 ___ ___-__-__"});
}

function alert_note(message){
    $.notify(
        {
            icon: 'glyphicon glyphicon-warning-sign',
            message: message
        },
        {
            type: 'danger',
            placement: {from: "top", align: "center"},
            animate: {enter: 'animated fadeInRight', exit: 'animated fadeOutRight'}
        });
}