<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'order']">
        <xsl:variable name="no_edit">
            <xsl:if test="(order/id > 0) and (/page/body/module[@name='CurentUser']/container/group_id = 2
                                        and /page/body/module[@name='orders']/container/routes/item/status_id != 1)">1
            </xsl:if>
        </xsl:variable>

        <!--group_id<xsl:value-of select="/page/body/module[@name='CurentUser']/container/group_id"/><br/>-->
        <!--status_id<xsl:value-of select="/page/body/module[@name='orders']/container/routes/item/status_id"/><br/>-->
        <!--order/id<xsl:value-of select="(order/id)"/><br/>-->
        <!--$no_edit<xsl:value-of select="$no_edit"/>-->
        <div class="row">
            <input class="today-date" type="hidden" value="{@today}"/>
            <form id="order_edit" class="order_edit" action="/orders/orderUpdate-{order/id}/without_menu-1/" method="post" name="main_form">
                <div class="col-sm-8">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-5">
                                    <xsl:if test="order/id > 0">
                                        <strong>Заказ №
                                            <xsl:value-of select="order/id"/>
                                        </strong>
                                        <br/>
                                    </xsl:if>
                                    <span class="his_time_now"/>
                                    <input type="hidden" id="time_now" value="{@time_now}"/>
                                </div>
                                <div class="col-sm-7" style="text-align:right;">
                                    <xsl:if test="(/page/body/module[@name='CurentUser']/container/group_id = 2)">
                                        <input class="form-control" type="text" name="title" onkeyup="check_user(this)" value="{client/item/title}" size="30" readonly=""/>
                                    </xsl:if>
                                    <xsl:if test="(/page/body/module[@name='CurentUser']/container/group_id != 2)">
                                        <select class="form-control select2" name="new_user_id" onchange="updUserStores(this)">
                                            <xsl:for-each select="users/item">
                                                <option value="{id}" phone="{phone}" sender="{name}" pay_type="{pay_type}" from="{from}" from_appart="{from_appart}" from_comment="{from_comment}">
                                                    <xsl:if test="../../order/id_user = id or (not(../../order/id_user) and ../../@user_id = id)">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                    </xsl:if>
                                                    <xsl:value-of select="title"/> [<xsl:value-of select="phone"/>]
                                                </option>
                                            </xsl:for-each>
                                        </select>
                                    </xsl:if>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <input id="order_id" type="hidden" name="order_id" value="{order/id}"/>
                            <input id="id_user" type="hidden" name="id_user" value="{order/id_user}"/>

                            <div class="input-group">
                                <div class="input-group-btn">
                                    <label class="btn btn-default" style="width: 50%; margin: 0;">
                                        <xsl:if test="order/is_peds = 0">
                                            <xsl:attribute name="class">btn btn-default active</xsl:attribute>
                                        </xsl:if>На автомобиле
                                        <input type="radio" name="is_peds" value="0" onchange="$('input[name=is_peds]').parent().removeClass('active'); $('input[name=is_peds]:checked').parent().addClass('active');calc_route(1);">
                                            <xsl:if test="order/is_peds = 0 or not(order/is_peds)">
                                                <xsl:attribute name="checked"/>
                                            </xsl:if>
                                        </input>
                                    </label>
                                    <label class="btn btn-default" style="width: 50%; margin: 0;">
                                        <xsl:if test="order/is_peds = 1">
                                            <xsl:attribute name="class">btn btn-default active</xsl:attribute>
                                        </xsl:if>Пешая доставка
                                        <input type="radio" name="is_peds" value="1" onchange="$('input[name=is_peds]').parent().removeClass('active'); $('input[name=is_peds]:checked').parent().addClass('active');calc_route(1);">
                                            <xsl:if test="order/is_peds = 1">
                                                <xsl:attribute name="checked"/>
                                            </xsl:if>
                                        </input>
                                    </label>
                                </div>
                            </div>

                            <div class="input-group" style="width:100%">
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-info">Дата заказа</span>
                                    <input class="date-picker order-route-data order-date" type="text" name="date" value="{order/date}" required="">
                                        <xsl:if test="not(order/date)">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="@today"/>
                                            </xsl:attribute>
                                        </xsl:if>
                                    </input>
                                    <script>
                                        $( ".order-date" ).on("dp.change change paste keyup", function() {
                                            update_time_ready($('.to_time_ready').get())
                                        });
                                    </script>
                                </div>
                                <div class="form-control" style="width: 50%;">
                                    <span class="order-add-title text-info"><xsl:if test="order/id_address = 0">
                                                    <xsl:attribute name="style">display:none;</xsl:attribute>
                                                </xsl:if>Откуда</span>
                                    <select class="order-route-data store_address js-store_address" name="store_id"><xsl:if test="order/id_address = 0">
                                                    <xsl:attribute name="style">display:none;</xsl:attribute>
                                                </xsl:if>
												
                                        <xsl:for-each select="stores/item">
                                            <option value="{id}">
                                                <xsl:if test="id = //order/id_address">
                                                    <xsl:attribute name="selected">selected
                                                    </xsl:attribute>
                                                </xsl:if>
                                                <xsl:value-of select="address"/>
                                            </option>
                                        </xsl:for-each>
										<option value="0">
											<xsl:if test="order/id_address = 0">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
										</option>
                                    </select>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <div class="funkyradio">
                                        <div class="funkyradio-info">
                                            <input type="checkbox" id="checkbox_hand_write" value="1" onchange="hand_write(this)">
                                                <xsl:if test="order/id_address = 0">
                                                    <xsl:attribute name="checked"/>
                                                </xsl:if>
                                            </input>
                                            <label for="checkbox_hand_write" style="margin: 0;border: 0;">
                                                <span>Ручной ввод</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hand_write" style="width:100%">
                                <xsl:if test="count(order/id_address) = 0 or order/id_address > 0">
                                    <xsl:attribute name="style">display:none</xsl:attribute>
                                </xsl:if>
                                <xsl:call-template name="from_address"/>
                            </div>
                            <div class="row">
                                <xsl:if test="order/id > 0">
                                    <div class="col-sm-2 col-xs-4">
                                        <label>Курьер:</label>
                                    </div>
                                    <div class="col-sm-10 col-xs-8">
                                        <select class="form-control select2" name="car_courier" title="Курьер">
                                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
                                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                                            </xsl:if>
                                            <option value="0">Не назначен</option>
                                            <xsl:variable name="courier_id" select="order/id_car"/>
                                            <xsl:for-each select="couriers/item">
                                                <option value="{id}">
                                                    <xsl:if test="id = $courier_id">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                    </xsl:if>
                                                    <xsl:value-of select="fio"/> (<xsl:value-of select="car_number"/>)
                                                </option>
                                            </xsl:for-each>
                                        </select>
                                    </div>
                                </xsl:if>
                            </div>
                            <hr/>
                            <label>Адреса доставки:</label>
                            <xsl:if test="@is_single != 1 and $no_edit != 1">
                                <button type="button" class="btn-clone btn btn-xs btn-success" title="Добавить адрес" onclick="clone_div_row($('.routes-block').last())"
                                        style="float:right; display:none">
                                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                                        <xsl:attribute name="style">float:right;</xsl:attribute>
                                    </xsl:if>
                                    <xsl:if test="position() != count(../../routes/item) and count(../../routes/item) != 0">
                                        <xsl:attribute name="disabled"/>
                                    </xsl:if>
                                    Добавить адрес
                                </button>
                            </xsl:if>
                            <xsl:call-template name="adresses">
                                <xsl:with-param name="no_edit" select="$no_edit"/>
                            </xsl:call-template>

                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 2">
                                <label>Заказчик</label>
                                <div class="input-group from-block" style="width:100%">
                                    <div class="form-control" style="width: 50%;">
                                        <span class="order-add-title text-warning">Заказчик ФИО</span>
                                        <input type="text" class="order-route-data" name="user_name" title="Заказчик" value="{@user_name}" required=""/>
                                    </div>
                                    <div class="form-control" style="width: 50%;">
                                        <span class="order-add-title text-warning">
                                            Телефон заказчика
                                        </span>
                                        <input type="text" class="order-route-data phone-number" name="user_phone" title="Телефон заказчика" value="{@user_phone}" required=""/>
                                    </div>
                                </div>
                            </xsl:if>
                        </div>
                        <div class="panel-footer">
                            <div class="btn-group" style="width: 100%;">
                                <a href="/" class="btn btn-warning">Выйти без сохранения</a>
                                <xsl:if test="(order/id > 0)">
                                    <span class="btn btn-primary" onclick="print_window({order/id})">Распечатать накладную</span>
                                    <script>
                                        function print_window(order_id) {
                                        window.open('/orders/naklad-'+order_id+'/', 'Накладная к заказу №'+order_id+'');
                                        }
                                    </script>
                                </xsl:if>
                                <xsl:if test="not(order/id > 0)">
                                    <span class="btn btn-primary disabled" onclick="bootbox.alert('Распечатать накладную можно будет после сохранения заказа')"
                                          title="Распечатать накладную можно будет после сохранения заказа" style="pointer-events: all;">Распечатать накладную
                                    </span>
                                </xsl:if>
                                <xsl:if test="$no_edit != 1">
                                    <span class="btn btn-info calc_route" onclick="calc_route(1)">Рассчитать маршрут</span>
                                    <input class="btn btn-success btn-submit" type="button" value="Сохранить заказ" onclick="return test_time_all_routes()"/>
                                </xsl:if>
                            </div>
                            <br/>
                            <xsl:if test="$no_edit = 1">
                                <div class="alert alert-warning" style="margin: 0 15px">
                                    Если вы хотетите отредактировать или отменить заказ свяжитесь, пожалуйста, с оператором по телефону:
                                    <b><a href="tel:+7-812-242-80-81" style="color:#000;"><span class="city-code">(812)</span> 242-80-81</a></b>
                                </div>
                            </xsl:if>
                            <xsl:if test="order/dk">
                                <div style="text-align:right" class="small text-muted">Изменен:
                                    <xsl:value-of select="order/dk"/>
                                </div>
                            </xsl:if>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 map-form">
                    <div class="map-container">
                        <div class="map-info">
                            <span id="ShortInfo"/>
                            <div class="map-full-info" id="viewContainer"/>
                        </div>
                        <div id="map" style="width: 100%; min-height: 500px"/>
                    </div>
                    <div class="alert alert-info">
                        <span class="delivery_sum"/>
                    </div>
                </div>
            </form>
            <div style="display:none">
                <xsl:for-each select="g_price/item">
                    <input id="g_price_{id}" class="g_prices" type="hidden" value="{value}" goods_id="{goods_id}" condition="{condition}" price="{price}" fixed="{fixed}" mult="{mult}"
                           rel="{goods_name}"/>
                </xsl:for-each>
                <xsl:for-each select="prices/item">
                    <input id="km_{id}" class="km_cost" type="hidden" value="{km_cost}" km_from="{km_from}" km_to="{km_to}"/>
                </xsl:for-each>
                <xsl:for-each select="add_prices/item">
                    <input id="km_{type}" type="hidden" value="{cost_route}"/>
                </xsl:for-each>
                <xsl:for-each select="times/node()">
                    <input id="{name()}_from" type="hidden" value="{from}"/>
                    <input id="{name()}_to" type="hidden" value="{to}"/>
                    <input id="{name()}_period" type="hidden" value="{period}"/>
                </xsl:for-each>
                <input id="user_peds_price" type="hidden" value="{//@user_peds_price}"/>
                <input id="user_fix_price" type="hidden" value="{//@user_fix_price}"/>
                <input id="user_max_price" type="hidden" value="{//@user_max_price}"/>
            </div>
        </div>
        <input id="order_edited" type="hidden" value="0"/>
        <input id="time_edited" type="hidden" value="0"/>
        <input id="order_id" type="hidden" value="{order/id}"/>
        <script>
            $('FORM').on('keyup keypress', function (e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });
            $('input, select').on('change', function () {
                $('#order_edited').val(1);
            });
            $('select.to_time_ready, select.to_time_ready_end, select.to_time, select.to_time_end, select.to_time_target').on('change', function () {
                $('#time_edited').val(1);
            });
        </script>
    </xsl:template>

    <xsl:template name="from_address">
        <div class="input-group from-block" rel="{position()}"  style="width:100%">
            <div class="form-control" style="width: 80%;">
                <span class="order-add-title text-info">Адрес отправления</span>
                <input type="search" class="order-route-data spb-streets" name="from[]" title="Улица, проспект и т.д." value="{order/from}" onchange="" autocomplete="off" required=""
                       coord="{order/from_coord}"/>
                <input type="hidden" class="coord" name="from_coord[]" value="{order/from_coord}"/>
            </div>
            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-info">кв/офис/помещ</span>
                <input type="text" class="order-route-data number" name="from_appart[]" title="Квартира" value="{order/from_appart}" required=""/>
            </div>

            <div class="form-control" style="width: 50%;">
                <span class="order-add-title text-warning">Отправитель ФИО</span>
                <input type="text" class="order-route-data" name="from_fio[]" title="Отправитель" value="{order/from_fio}" onkeyup="$('[name=user_name]').val($(this).val())" required=""/>
            </div>
            <div class="form-control" style="width: 50%;">
                <span class="order-add-title text-warning">
                    Телефон отправителя
                </span>
                <input type="text" class="order-route-data phone-number" name="from_phone[]" title="Телефон отправителя" value="{order/from_phone}"
                       onkeyup="$('[name=user_phone]').val($(this).val())" required=""/>
            </div>
            <textarea name="from_comment[]" class="form-control" title="Комментарий" placeholder="Примечания к адресу" style="width: 100%;">
                <xsl:value-of select="order/from_comment"/>
            </textarea>
        </div>
    </xsl:template>

    <xsl:template name="adresses">
        <xsl:param name="no_edit"/>
        <xsl:for-each select="routes/item">
            <xsl:call-template name="routes">
                <xsl:with-param name="no_edit" select="$no_edit"/>
            </xsl:call-template>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="routes">
        <xsl:param name="no_edit"/>
        <div class="input-group routes-block" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <div class="form-control" style="width: 80%;">
                <span class="order-add-title text-info">Адрес доставки</span>
                <input type="search" class="order-route-data spb-streets" name="to[]" title="Улица, проспект и т.д." value="{to}" onchange="calc_route(1)" autocomplete="off" required=""
                       coord="{to_coord}">
                    <xsl:attribute name="value">
                        <xsl:value-of select="to"/>
                        <xsl:if test="to_house != ''">, д.<xsl:value-of select="to_house"/>
                        </xsl:if>
                    </xsl:attribute>
                </input>
                <input type="hidden" class="coord" name="to_coord[]" value="{to_coord}"/>
            </div>

            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-info">кв/офис/помещ</span>
                <input type="text" class="order-route-data number" name="to_appart[]" title="Квартира" value="{to_appart}" required=""/>
            </div>


            <div class="form-control" style="width: 40%;">
                <span class="order-add-title text-warning">Получатель ФИО</span>
                <input type="text" class="order-route-data" name="to_fio[]" title="Получатель" value="{to_fio}" required=""/>
            </div>
            <div class="form-control" style="width: 30%;">
                <span class="order-add-title text-warning">Телефон получателя</span>
                <input type="text" class="order-route-data" name="to_phone[]" title="Телефон получателя" value="{to_phone}" required=""/>
            </div>
            <div class="form-control" style="width: 30%;">
                <span class="order-add-title text-warning">
                </span>
                <div class="funkyradio">
                    <div class="funkyradio-success">
                        <input type="checkbox" id="checkbox_{position()}" class="target" name="target" value="1" onchange="calc_route(1); target_time_show()">
                            <xsl:if test="../../order/target = 1">
                                <xsl:attribute name="checked"/>
                            </xsl:if>
                        </input>
                        <label for="checkbox_{position()}">
                            <span>К точному времени</span>
                        </label>
                    </div>
                </div>
                <label style="width:100%; text-align:center">
                    <input type="checkbox" class="order-route-data target" name="target" value="1" onchange="calc_route(1); target_time_show()" style="width:32px;margin:0">
                        <xsl:if test="../../order/target = 1">
                            <xsl:attribute name="checked"/>
                        </xsl:if>
                    </input>
                </label>
            </div>

            <!-- Строка времени -->

            <div class="form-control" style="width: 25%;">
                <span class="order-add-title text-danger">
                    Можно забрать с
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_ready</xsl:with-param>
                    <xsl:with-param name="select_name">to_time_ready[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время готовности</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time_ready"/>
                    <xsl:with-param name="select_onchange">update_time_ready(this)</xsl:with-param>
                </xsl:call-template>
            </div>
            <div class="form-control" style="width: 25%;">
                <span class="order-add-title text-danger">
                    Можно забрать по
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_ready_end</xsl:with-param>
                    <xsl:with-param name="select_name">to_time_ready_end[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время готовности</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time_ready_end"/>
                    <xsl:with-param name="select_onchange">update_time_ready_end(this)</xsl:with-param>
                </xsl:call-template>
            </div>

            <div class="form-control target_select" style="width: 50%;">
                <xsl:if test="../../order/target != 1 or not(../../order/target)">
                    <xsl:attribute name="style">width: 50%; display:none;</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-primary">
                    Доставить К
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_target</xsl:with-param>
                    <xsl:with-param name="select_name">target_time[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time"/>
                    <xsl:with-param name="select_onchange">test_time_routes_add(); time_routes_set(this); $('.to_time').val($(this).val());$('.to_time_end').val($(this).val());</xsl:with-param>
                </xsl:call-template>
            </div>
            <div class="form-control period_select" style="width: 25%;">
                <xsl:if test="../../order/target = 1">
                    <xsl:attribute name="style">width: 33%; display:none;</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-primary">
                    Доставить С
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time</xsl:with-param>
                    <xsl:with-param name="select_name">to_time[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки с</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time"/>
                    <xsl:with-param name="select_onchange">test_time_routes_add(); $('.to_time_target').val($(this).val());</xsl:with-param>
                </xsl:call-template>
            </div>
            <div class="form-control period_select" style="width: 25%;">
                <xsl:if test="../../order/target = 1">
                    <xsl:attribute name="style">width: 33%; display:none;</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-primary">
                    Доставить По
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_end</xsl:with-param>
                    <xsl:with-param name="select_name">to_time_end[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки по</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time_end"/>
                    <xsl:with-param name="select_onchange">test_time_routes_add()</xsl:with-param>
                </xsl:call-template>
            </div>
            <!-- Строка оплаты -->
            <div class="form-control form_goods">
                <span class="order-add-title text-success">
                    Что доставляем?
                </span>
                <select class="order-route-data goods_type" name="goods_type[]" title="Тип товара" onchange="calc_route(1);" required="">
                    <xsl:variable name="goods_type" select="goods_type"/>
                    <option value=""></option>
                    <xsl:for-each select="../../goods/item">
                        <option value="{goods_id}">
                            <xsl:if test="goods_id = $goods_type">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="goods_name"/>
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <div class="form-control form_goods_count">
                <span class="order-add-title text-success">
                    Кол-во
                </span>
                <input type="number" class="order-route-data number goods_val" name="goods_val[]" title="Количество товара" value="{goods_val}" onchange="calc_route(1);" min="0" required=""/>
            </div>

            <div class="form-control money_to" style="width: 15%;" title="Общая сумма наличных, которую необходимо забрать у получателя, включая  цену доставки если ее оплачивает получатель.">
                <span class="order-add-title text-success">
                    ₽ от получателя
                </span>
                <input type="number" class="order-route-data number cost_tovar" name="cost_tovar[]" value="{cost_tovar}" onchange="re_calc(this)" min="0" required=""/>
            </div>
            <div class="form-control price_route" style="width: 15%;">
                <span class="order-add-title text-success">
                    Цена доставки
                </span>
                <input type="text" class="order-route-data number cost_route" name="cost_route[]" title="Стоимость доставки" value="{cost_route}" onkeyup="re_calc(this)" required="">
                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
                        <xsl:attribute name="readonly">readonly</xsl:attribute>
                    </xsl:if>
                </input>
            </div>
            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 2">
                <div class="form-control" style="width: 20%;">
                    <span class="order-add-title text-success">
                        ₽ курьер
                    </span>
                    <input type="text" class="order-route-data number cost_car" name="cost_car[]" title="Заработок курьера" value="{cost_car}" onkeyup="re_calc(this)"/>
                </div>
            </xsl:if>
            <div class="form-control" style="width: 20%;" title="Выберете способ оплаты наших услуг.">
                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
                    <xsl:attribute name="style">width:40%</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-success">
                    Способ оплаты доставки
                </span>
                <xsl:if test="//@user_pay_type > 0">
                    <input type="hidden" name="pay_type[]" value="{//@user_pay_type}"/>
                </xsl:if>
                <select class="order-route-data pay_type" name="pay_type[]" onchange="re_calc(this)" required="">
                    <xsl:if test="//@user_pay_type > 0">
                        <xsl:attribute name="disabled">disabled</xsl:attribute>
                    </xsl:if>
                    <xsl:variable name="pay_type" select="pay_type_id"/>
                    <xsl:variable name="user_pay_type" select="//@user_pay_type"/>
                    <option value=""></option>
                    <xsl:for-each select="../../pay_types/item">
                        <option value="{id}">
                            <xsl:if test="id = $pay_type or (not($pay_type) and $user_pay_type = id)">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="pay_type"/>
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <div class="form-control" style="width: 20%; display:none;">
                <span class="order-add-title text-success">
                    Общая сумма
                </span>
                <input type="text" class="order-route-data number cost_all" title="Инкассация" disabled="">
                    <xsl:if test="string(number(cost_route)+number(cost_tovar)) != 'NaN'">
                        <xsl:attribute name="value">
                            <xsl:value-of select="(number(cost_route)+number(cost_tovar))"/>
                        </xsl:attribute>
                    </xsl:if>
                </input>
            </div>
            <textarea name="comment[]" class="form-control" title="Комментарий" placeholder="Примечания к заказу">
                <xsl:if test="../../order/id > 0">
                    <xsl:attribute name="style">width: 80%;</xsl:attribute>
                </xsl:if>
                <xsl:value-of select="comment"/>
            </textarea>

            <xsl:if test="../../order/id > 0">
                <div class="form-control" style="width: 20%;">
                    <span class="order-add-title text-success">
                        Статус
                    </span>

                    <select class="order-route-data" name="status[]" title="Статус заказа">
                        <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2 and status_id != 1">
                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                        </xsl:if>
                        <xsl:variable name="status_id" select="status_id"/>
                        <xsl:choose>
                            <xsl:when test="/page/body/module[@name='CurentUser']/container/group_id = 2 and status_id = 1">
                                <xsl:for-each select="../../statuses/item">
                                    <!-- Либо Новый либо отмена -->
                                    <xsl:if test="id = 1 or id = 5">
                                        <option value="{id}">
                                            <xsl:if test="id = $status_id">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>
                                            <xsl:value-of select="status"/>
                                        </option>
                                    </xsl:if>
                                </xsl:for-each>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:for-each select="../../statuses/item">
                                    <option value="{id}">
                                        <xsl:if test="id = $status_id">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="status"/>
                                    </option>
                                </xsl:for-each>
                            </xsl:otherwise>
                        </xsl:choose>
                    </select>
                </div>
            </xsl:if>

            <xsl:if test="../../@is_single != 1 and $no_edit != 1">
                <div class="add_buttons" style="vertical-align: top; display:none">
                    <button type="button" class="btn-delete btn btn-sm btn-danger" title="Удалить" onclick="delete_div_row(this)">
                        <xsl:if test="position() = 1">
                            <xsl:attribute name="disabled"/>
                        </xsl:if>
                        <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"/>
                    </button>
                </div>
            </xsl:if>
        </div>
    </xsl:template>
    <xsl:template name="time_selector">
        <xsl:param name="select_class"/>
        <xsl:param name="select_name"/>
        <xsl:param name="select_title"/>
        <xsl:param name="select_value"/>
        <xsl:param name="select_onchange"/>
        <select name="{$select_name}" class="{$select_class}" title="{$select_title}" required="">
            <xsl:if test="$select_onchange != ''">
                <xsl:attribute name="onchange">
                    <xsl:value-of select="$select_onchange"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="$select_name = 'to_time_ready_end[]'">
                <option value="-">∞</option>
            </xsl:if>
            <xsl:for-each select="../../timer/element">
                <option value="{.}">
                    <xsl:if test=". = $select_value">
                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>
                    <xsl:if test="not(../../order/id) and . = ../../@time_now_five and $select_name != 'to_time_ready_end[]'">
                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>
                    <xsl:value-of select="."/>
                </option>
            </xsl:for-each>
        </select>
    </xsl:template>
</xsl:stylesheet>
