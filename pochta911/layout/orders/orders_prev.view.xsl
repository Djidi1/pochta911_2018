<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <form method="post" style="margin-bottom: 2px;">
                <div class="row">
                    <div class="col-sm-9">
                        <span class="btn btn-success" onclick="open_bootbox_dialog('/orders/order-0/')" title="Добавить заказ">
                            <span class="glyphicon glyphicon-flag"> </span> Новый заказ
                        </span>
                        <span class="btn btn-success" onclick="popup_excel('orders/excel-1')" title="Выгрузить в Excel">
                            <i class="fa fa-file-excel-o" aria-hidden="true"> </i> Excel</span>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group" style="float:right">
                            <input type="text" class="form-control" id="end_date" name="date_to" value="{@date_to}" style="text-align:center" />
                            <span class="input-group-btn">
                                <button class="btn btn-info">Обновить</button>
                            </span>
                        </div>
                        <script>
                            $(function () {
                                $('#end_date').datetimepicker({format: 'L', locale: 'ru'});
                            });
                        </script>
                    </div>
                </div>
            </form>

            <table class="table table-striped orders-data-table">
                <thead>
                    <th>Заказ</th>
                    <th>Маршрут</th>
                    <th>Получатель</th>
                    <th>Время доставки</th>
                    <th>Стоимость</th>
                    <th>Статус</th>
                    <!--<th/>-->
                </thead>
                <tbody>
                    <xsl:for-each select="orders/item/route/array">
                        <tr class="order_route_{id_route} order_{../../id}" rel="{id_route}">
                            <td>
                                <div class="row">
                                    <div class="col-md-2">
                                        Готовность:<br/>
                                        <b>
                                            <xsl:value-of select="../../ready"/>
                                        </b>
                                    </div>
                                    <div class="col-md-3">
                                        Заказ №
                                        <xsl:value-of select="../../id"/>
                                        <br/>
                                        <b>
                                            <xsl:value-of select="../../title"/>
                                        </b>
                                        <br/>
                                        <i>
                                            <xsl:value-of select="../../name"/>
                                        </i>
                                    </div>
                                    <div class="col-md-3">
                                        Адрес магазина:<br/>
                                        <b><xsl:value-of select="../../from"/></b>
                                        <br/>
                                        <i>
                                            <xsl:value-of select="../../addr_comment"/>
                                        </i>
                                    </div>
                                    <div class="col-md-4">
                                        <xsl:if test="status_id = 1">
                                        <div class="btn-group" role="group" aria-label="Управление заказом">
                                            <a href="/orders/order-{../../id}/" title="редактировать" class="btn btn-success btn-sm">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>
                                            </a>
                                            <a href="/orders/orderBan-{../../id}/" title="удалить" class="btn btn-danger btn-sm">
                                                <xsl:attribute name="onClick">return confirm('Вы действительно хотите удалить заказ <xsl:value-of select="id"/>?');
                                                </xsl:attribute>
                                                <span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>
                                            </a>
                                        </div>
                                        </xsl:if>
                                        Курьер:<br/>
                                        <b><xsl:value-of select="../../fio_car"/></b>
                                        <br/>
                                        <i><xsl:value-of select="../../car_number"/></i>
                                        <!--<br/>-->
                                        <!--<i>-->
                                            <!--За рулем: <xsl:value-of select="../../fio_courier"/>-->
                                        <!--</i>-->
                                        <!--<div class="btn-group" role="group" aria-label="Изменение курьера">-->
                                            <!--<a href="#" title="Назначить курьера" class="btn btn-info btn-sm" onclick="chg_courier({../../id})">-->
                                                <!--<i class="fa fa-car" aria-hidden="true"> </i> Курьер-->
                                            <!--</a>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                                <xsl:if test="../../comment != ''">
                                    <div class="alert alert-success">
                                        <xsl:value-of select="../../comment"/>
                                    </div>
                                </xsl:if>
                            </td>
                            <td class="order_info">
                                <xsl:attribute name="rel">{"order_id": "<xsl:value-of select="../../id"/>","from": "<xsl:value-of select="../../from"/>","ready": "<xsl:value-of select="../../ready"/>","to_addr": "<xsl:value-of select="to"/>, д.<xsl:value-of select="to_house"/>, корп.<xsl:value-of select="to_corpus"/>, кв.<xsl:value-of select="to_appart"/>","to_time": "<xsl:value-of select="to_time"/>","to_fio": "<xsl:value-of select="to_fio"/>","to_phone": "<xsl:value-of select="to_phone"/>","cost": "<xsl:value-of select="cost_route"/>"}</xsl:attribute>
                                <xsl:value-of select="to"/>, <xsl:value-of select="to_house"/>, <xsl:value-of select="to_corpus"/>,
                                <xsl:value-of select="to_appart"/>
                            </td>
                            <td>
                                <xsl:value-of select="to_fio"/>
                                <xsl:text> </xsl:text>
                                <nobr>
                                    <b>
                                        <xsl:value-of select="to_phone"/>
                                    </b>
                                </nobr>
                            </td>
                            <td>
                                <b>
                                    <xsl:value-of select="to_time"/>
                                </b>
                            </td>
                            <td>
                                <xsl:value-of select="cost_tovar"/> + <xsl:value-of select="cost_route"/> = <xsl:value-of select="number(cost_route)+number(cost_tovar)"/>
                            </td>
                            <td>
                                <xsl:value-of select="status"/>
                            </td>
                            <!--<td width="80px" align="center">-->
                                <!--<div class="btn-group" role="group" aria-label="Изменение статуса">-->
                                    <!--<a href="#" title="Изменить статус" class="btn btn-warning btn-sm" onclick="chg_status({id_route},{../../id})">-->
                                        <!--<span class="glyphicon glyphicon-flag" aria-hidden="true"> </span> Стутус-->
                                    <!--</a>-->
                                <!--</div>-->
                            <!--</td>-->
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </xsl:if>
        <xsl:if test="//page/@isAjax = 1">
            Ajax!
        </xsl:if>
    </xsl:template>
    <xsl:template name="datepickers">
        <div class="input-daterange input-group" id="datepicker">
            <input type="text" class="input-sm form-control" id="start_date" name="from" value="{@from}" />
            <span class="input-group-addon">to</span>
            <input type="text" class="input-sm form-control" id="end_date" name="to" value="{@to}" />
        </div>
        <script type="text/javascript">
            $(function () {
            $('#start_date').datetimepicker({format: 'L', locale: 'ru'});
            $('#end_date').datetimepicker({format: 'L', locale: 'ru',
            useCurrent: false //Important! See issue #1075
            });
            $("#start_date").on("dp.change", function (e) {
            $('#end_date').data("DateTimePicker").minDate(e.date);
            });
            $("#end_date").on("dp.change", function (e) {
            $('#start_date').data("DateTimePicker").maxDate(e.date);
            });
            $("#start_date").on("dp.show", function (e) {
            $('#start_date').data("DateTimePicker").maxDate(e.date);
            });
            $("#end_date").on("dp.show", function (e) {
            $('#end_date').data("DateTimePicker").minDate(e.date);
            });
            });
        </script>
    </xsl:template>
</xsl:stylesheet>