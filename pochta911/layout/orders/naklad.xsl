<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'naklad']">
        <div class="well naklad-panel">
            <table class="table borderless table-no-bordered" style="margin-top: -15px;">
                <tr>
                    <td class="col-xs-3" style="vertical-align: middle;">Дата доставки:
                        <b>
                            <xsl:value-of select="order/date"/>
                        </b>

                    </td>
                    <td class="col-xs-6" style="text-align: center;vertical-align: bottom;">
                        <h4>Накладная к заказу №<b>
                                <xsl:value-of select="order/id"/>
                            </b>
                        </h4>
                    </td>
                    <td class="col-xs-3" style="text-align: right;vertical-align: bottom;">
                        <img src="/images/logo_black.png"/>
                    </td>
                </tr>
            </table>

            <div class="row">
                <div class="col-xs-6" style="padding-right: 5px;">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <b>Отправитель</b>
                        </div>
                        <div class="panel-body">
                            <div class="input-group" style="width: 100%">
                                <div class="form-control" style="width: 60%;">
                                    <span class="order-add-title text-success">Адрес доставки</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="client/address"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-success">дом/корп/строение</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="client/to_house"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-success">кв/офис/помещ</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="client/to_appart"/>
                                    </div>
                                </div>

                                <div class="form-control" style="width: 35%;">
                                    <span class="order-add-title text-success">Отправитель</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="client/name"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 25%;">
                                    <span class="order-add-title text-success">
                                        Телефон
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="client/phone"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-success">
                                        Забрать с
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_time_ready"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-success">
                                        Забрать по
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_time_ready_end"/>
                                    </div>
                                </div>

                                <div class="form-control" style="width: 100%;">
                                    <span class="order-add-title text-success">
                                        Примечания
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="client/comment"/>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6" style="padding-left: 5px;">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <b>Получатель</b>
                        </div>
                        <div class="panel-body">
                            <div class="input-group" style="width: 100%">
                                <div class="form-control" style="width: 60%;">
                                    <span class="order-add-title text-info">Адрес</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-info">дом/корп/строение</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_house"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-info">кв/офис/помещ</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_appart"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 35%;">
                                    <span class="order-add-title text-info">Получатель ФИО</span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_fio"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 25%;">
                                    <span class="order-add-title text-info">
                                        Телефон
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_phone"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-info">
                                        Доставить с
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_time"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-info">
                                        Доставить по
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/to_time_end"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 100%;">
                                    <span class="order-add-title text-info">
                                        Примечания
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/comment"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6" style="padding-right: 5px;">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <b>Описание отправления</b>
                        </div>
                        <div class="panel-body">
                            <div class="input-group" style="width: 100%">
                                <div class="form-control" style="width: 70%;">
                                    <span class="order-add-title text-success">
                                        Характер груза
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/goods_name"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-success">
                                        Кол-во
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/goods_val"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6" style="padding-left: 5px;">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <b>Информация об оплате</b>
                        </div>
                        <div class="panel-body">
                            <div class="input-group" style="width: 100%">
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-info">
                                        Тип оплаты
                                    </span>
                                    <div class="naklad-route-data">
                                        Наличная<!--<xsl:value-of select="routes/item/pay_type_cash"/>-->
                                    </div>
                                </div>
                                <div class="form-control" style="width: 20%;">
                                    <span class="order-add-title text-info">
                                        Цена доставки
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/cost_route"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 50%;">
                                    <span class="order-add-title text-info">
                                        Оплата доставки
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="routes/item/pay_type"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <b>Подписи сторон</b>
                        </div>
                        <div class="panel-body">
                            <div class="input-group" style="width: 100%">
                                <div class="form-control" style="width: 40%;">
                                    <span class="order-add-title text-warning">
                                        Отправитель
                                    </span>
                                    <div class="naklad-route-data">
                                        <xsl:value-of select="client/name"/>
                                    </div>
                                </div>
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-warning">
                                        Подпись
                                    </span>
                                </div>
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-warning">
                                        Дата
                                    </span>
                                </div>

                                <div class="form-control" style="width: 40%;">
                                    <span class="order-add-title text-warning">
                                        Курьер
                                    </span>
                                    <div class="naklad-route-data">
                                        <!--<xsl:value-of select="order/fio_car"/> / <xsl:value-of select="order/car_number"/>-->
                                    </div>
                                </div>
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-warning">
                                        Подпись
                                    </span>
                                </div>
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-warning">
                                        Дата
                                    </span>
                                </div>

                                <div class="form-control" style="width: 40%;">
                                    <span class="order-add-title text-warning">
                                        Получатель (ФИО/должность)
                                    </span>
                                    <div class="naklad-route-data">
                                        <!--<xsl:value-of select="routes/item/to_fio"/>-->
                                    </div>
                                </div>
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-warning">
                                        Подпись
                                    </span>
                                </div>
                                <div class="form-control" style="width: 30%;">
                                    <span class="order-add-title text-warning">
                                        Дата
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table" style="margin-bottom: -15px;">
                <tr>
                    <td class="col-xs-4" style="">
                        <h4><b>POCHTA911.RU</b></h4></td>
                    <td class="col-xs-4" style="text-align: center;">
                        <h4><b>(812) 242-80-81</b></h4>
                    </td>
                    <td class="col-xs-4" style="text-align: right; vertical-align: middle">
                        <xsl:if test="order/dk">
                            <div style="text-align:right" class="small text-muted">Изменен:
                                <xsl:value-of select="order/dk"/>
                            </div>
                        </xsl:if>
                    </td>
                </tr>
            </table>
        </div>
    </xsl:template>
</xsl:stylesheet>
