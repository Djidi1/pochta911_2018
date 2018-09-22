<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <div id="counter_form" style="float: right; display:none">
                <form name="counter" class="counter">Обновление через <input id="counter_input" type="text" name="d2" disabled="" class="counter_input" style="background-color: transparent;border: none;color: red;width:40px;"/>
                </form>
                <script>
                    refresh_page('/orders/LogistList-1/');
                </script>
            </div>
            <form id="form_orders" method="post" style="margin-bottom: 2px;">
                <div class="row">
                    <div class="col-sm-6">
                        <a class="btn btn-success btn-sm" href="/orders/order-0/" title="Добавить заказ" style="margin-bottom:2px">
                            <span class="glyphicon glyphicon-flag"> </span>
                            <span>Новый заказ</span>
                        </a>
						<br/>
                        <strong class="hide-mobile">Статусы: </strong>
                        <xsl:for-each select="statuses/item">
                            <label class="btn btn-default btn-xs" style="margin-right:2px;" onchange="filter_table()">
                                <input class="statuses" type="checkbox" aria-label="" value="{id}"/>
                                <xsl:text> </xsl:text>
                                <span style="vertical-align: text-bottom;"><xsl:value-of select="status"/></span>
                            </label>
                        </xsl:for-each>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group input-group-sm" style="float:right;width: inherit;">
                            <span class="input-group-addon" id="basic-addon1">Дата</span>
                            <input type="text" class="form-control" id="end_date" name="date_to" value="{@date_to}" style="text-align:center; width: 90px;" onchange="$('#form_orders').submit()"/>
                            <span class="input-group-btn">
                                <span class="btn btn-success" onclick="popup_excel('orders/excel-1/logist-1')" title="Выгрузить в Excel">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"> </i> Выгрузить в Excel</span>
                            </span>
                        </div>
                        <script>
                            $(function () {
                                $('#end_date').datetimepicker({format: 'L', locale: 'ru'}).on("dp.change", function (e) {
                                    $('#form_orders').submit();
                                });
                            });
                        </script>
                    </div>
                </div>
            </form>
            <div id="orders_table">
                <xsl:call-template name="orders_table"/>
            </div>
        </xsl:if>
        <xsl:if test="//page/@isAjax = 1">
            <xsl:call-template name="orders_table"/>
        </xsl:if>
    </xsl:template>
    <xsl:template name="orders_table">
        <table class="table table-striped table-hover table-bordered new-logist-data-table">
            <thead>
                <!--<th>#</th>-->
                <th>Заказ</th>
                <th class="hide-mobile">Готов</th>
                <th>Время доставки</th>
                <th>Адрес приема и контакт</th>
                <th class="hide-mobile">Компания</th>
                <th>Адрес доставки</th>
                <!--<th>Телефон</th>-->
                <!--<th>Получатель</th>-->
                <th>Статус</th>
                <th class="hide-mobile">Курьер и телефон</th>
                <th class="hide-mobile">Стоимость доставки</th>
                <th class="hide-mobile">Инкассация</th>
                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                    <!--<th>Инкассация</th>-->
                    <!--<th>% инкассации</th>-->
                    <!--<th>Заработок курьера</th>-->
                    <!--<th>Заработок компании</th>-->
                </xsl:if>
                <th class="hide-mobile">Прим. заказ</th>
                <!--<th>Прим. адрес</th>-->
                <th class="hide-mobile"/>
            </thead>
            <!--<tfoot>-->
            <!--<tr>-->
            <!--&lt;!&ndash;<th/>&ndash;&gt;-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--<th/>-->
            <!--</tr>-->
            <!--</tfoot>-->
            <tbody>
                <xsl:for-each select="orders/item/route/array">
                    <tr onclick="open_bootbox_dialog('/orders/order-{../../id}/');">
                        <xsl:attribute name="class">pointer status_<xsl:value-of select="status_id"/> order_route_<xsl:value-of select="id_route"/> order_<xsl:value-of select="../../id"/>
                            <xsl:if test="../../car_accept != ''"> info</xsl:if>
                        </xsl:attribute>
                        <!--<td class="text text-muted"><xsl:value-of select="position()"/></td>-->
                        <td>
                            <xsl:value-of select="../../id"/>
                        </td>
                        <td class="hide-mobile"><nobr><xsl:value-of select="to_time_ready"/>-<xsl:value-of select="to_time_ready_end"/></nobr></td>
                        <td><nobr><xsl:value-of select="to_time"/>-<xsl:value-of select="to_time_end"/></nobr></td>
                        <td><xsl:value-of select="../../address"/>
                            <br/>
                            <i>
                                <xsl:value-of select="../../addr_comment"/>
                            </i>
                        </td>
                        <td class="hide-mobile"><xsl:value-of select="../../title"/></td>
                        <td><span class="no-br"><b><xsl:value-of select="to"/>, <xsl:value-of select="to_house"/>, <xsl:value-of select="to_appart"/></b></span></td>
                        <!--<td><nobr>-->
                        <!--<b>-->
                        <!--<xsl:value-of select="to_phone"/>-->
                        <!--</b>-->
                        <!--</nobr></td>-->
                        <!--<td><xsl:value-of select="to_fio"/></td>-->
                        <td class="order_info">
                            <xsl:attribute name="rel">{"order_id": "<xsl:value-of select="../../id"/>","from": "<xsl:value-of select="../../address"/>","ready": "<xsl:value-of select="../../ready"/>","to_addr": "<xsl:value-of select="to"/>, д.<xsl:value-of select="to_house"/>, кв.<xsl:value-of select="to_appart"/>","to_time": "<xsl:value-of select="to_time"/>","to_fio": "<xsl:value-of select="to_fio"/>","to_phone": "<xsl:value-of select="to_phone"/>","cost": "<xsl:value-of select="cost_route"/>"}</xsl:attribute>
                            <xsl:attribute name="class">
                                order_info
                                <xsl:if test="status_id = 3"> warning</xsl:if>
                                <xsl:if test="status_id = 4"> success</xsl:if>
                                <xsl:if test="status_id = 5"> danger</xsl:if>
                            </xsl:attribute>
                            <xsl:value-of select="status"/>
                        </td>
                        <td class="hide-mobile">
                            <xsl:value-of select="../../fio_car"/> (<xsl:value-of select="../../car_number"/>)
                        </td>

                        <td class="hide-mobile"><xsl:value-of select="cost_route"/></td>
                        <xsl:choose>
                            <xsl:when test="pay_type = 2">
                                <td title="({cost_route} + {cost_tovar})" class="hide-mobile">
                                    <xsl:value-of select="(number(cost_route)+number(cost_tovar))"/>
                                </td>
                            </xsl:when>
                            <xsl:when test="pay_type = 3 or (pay_type = 1 and cost_tovar > 0)">
                                <td title="{cost_tovar}" class="hide-mobile">
                                    <xsl:value-of select="cost_tovar"/>
                                </td>
                            </xsl:when>
                            <xsl:when test="(pay_type = 1 and cost_tovar = 0)">
                                <td title="{cost_route}" class="hide-mobile">
                                    <xsl:value-of select="cost_route"/>
                                </td>
                            </xsl:when>
                            <xsl:otherwise><td class="hide-mobile"><xsl:value-of select="cost_tovar"/></td></xsl:otherwise>
                        </xsl:choose>

                        <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                            <!--<td><xsl:value-of select="number(cost_route)+number(cost_tovar)"/></td>-->
                            <!--<td>-->
                            <!--<xsl:value-of select="((number(cost_route)+number(cost_tovar)) * number(../../inkass_proc)) div 100"/>-->
                            <!--(<xsl:value-of select="../../inkass_proc"/>%)-->
                            <!--</td>-->
                            <!--<td><xsl:value-of select="number(cost_route) * 0.75"/></td>-->
                            <!--<td><xsl:value-of select="number(cost_route) * 0.25"/></td>-->
                        </xsl:if>
                        <td class="hide-mobile">
                            <xsl:value-of select="comment"/>
                        </td>
                        <td style="width:90px" class="hide-mobile">
                            <xsl:if test="status_id = 1">
                                <div class="btn-group">
                                    <div title="Изменить статус" class="btn btn-danger btn-xs chg-status" onclick="event.stopPropagation(); cancel_order({../../id})">
                                        <span class="glyphicon glyphicon-flag" aria-hidden="true"> </span> отменить
                                    </div>
                                </div>
                            </xsl:if>
                        </td>
                        <!--<td>-->
                        <!--<xsl:value-of select="comment"/>-->
                        <!--</td>-->
                        <!--<td style="width:90px">-->
                        <!--<div class="btn-group">-->
                        <!--&lt;!&ndash;<div onclick="open_dialog('/orders/order-{../../id}/')" class="btn btn-success btn-xs chg-status" title="редактировать">&ndash;&gt;-->
                        <!--&lt;!&ndash;<span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>&ndash;&gt;-->
                        <!--&lt;!&ndash;</div>&ndash;&gt;-->
                        <!--&lt;!&ndash;<a href="/orders/order-{../../id}/" class="btn btn-success btn-xs chg-status" title="редактировать" target="_blank">&ndash;&gt;-->
                        <!--&lt;!&ndash;<span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>&ndash;&gt;-->
                        <!--&lt;!&ndash;</a>&ndash;&gt;-->
                        <!--<div title="Назначить курьера" class="btn btn-info btn-xs chg-status" onclick="chg_courier({../../id})">-->
                        <!--<i class="fa fa-car" aria-hidden="true"> </i>-->
                        <!--</div>-->
                        <!--<div title="Изменить статус" class="btn btn-warning btn-xs chg-status" onclick="chg_status({id_route},{../../id})">-->
                        <!--<span class="glyphicon glyphicon-flag" aria-hidden="true"> </span>-->
                        <!--</div>-->
                        <!--</div>-->
                        <!--</td>-->
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>
    </xsl:template>
</xsl:stylesheet>