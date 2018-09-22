<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'index']">
        <xsl:if test="//page/@isAjax != 1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Калькулятор доставки</h3>
                        </div>
                        <div id="viewListlang" class="panel-body">
                            <xsl:call-template name="calcOnMain"/>
                            <div style="display:none">
                                <xsl:for-each select="prices/item">
                                    <input id="km_{id}" class="km_cost" type="hidden" value="{km_cost}" km_from="{km_from}" km_to="{km_to}"/>
                                </xsl:for-each>
                                <xsl:for-each select="add_prices/item">
                                    <input id="km_{type}" type="hidden" value="{cost_route}"/>
                                </xsl:for-each>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </xsl:if>
    </xsl:template>
    <xsl:template name="calcOnMain">
        <div class="col-sm-4">
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-info-sign"/>
                <i> Введите адреса для моментального расчёта стоимости доставки:</i>
            </div>
            <hr/>
            <div class="row">
                <div class="col-xs-12">
                    <div class="input-group routes-block" rel="{position()}" style="width: 100%;">
                        <div class="form-control" style="width: 100%;">
                            <span class="order-add-title text-info">Адрес отправления</span>
                            <input type="search" class="order-route-data spb-streets" name="to[]" title="Улица, проспект и т.д." onchange="" autocomplete="off" required=""/>
                        </div>
                        <!--<div class="form-control" style="width: 30%;">-->
                            <!--<span class="order-add-title text-info">дом/корп.</span>-->
                            <!--<select type="text" class="order-route-data to_house number" name="to_house[]" title="Дом/Корпус" onchange="calc_route(1)" autocomplete="off" required="" AOGUID=""/>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-xs-12">
                    <div class="input-group routes-block" rel="{position()}" style="width: 100%;">
                        <div class="form-control" style="width: 100%;">
                            <span class="order-add-title text-info">Адрес доставки</span>
                            <input type="search" class="order-route-data spb-streets" name="to[]" title="Улица, проспект и т.д." onchange="" autocomplete="off" required=""/>
                        </div>
                        <!--<div class="form-control" style="width: 30%;">-->
                            <!--<span class="order-add-title text-info">дом/корп.</span>-->
                            <!--<select type="text" class="order-route-data to_house number" name="to_house[]" title="Дом/Корпус" onchange="calc_route(1)" autocomplete="off" required="" AOGUID=""/>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
            <hr/>
            <span class="btn btn-info calc_route" onclick="calc_route(1)">Рассчитать доставку</span>
            <div class="delivery_sum_title">---</div>
            <hr/>
            <div class="alert alert-info">
                <span class="glyphicon glyphicon-ok-sign"/>
                <xsl:text> </xsl:text>
                <i><a href="#" onclick="window.top.opendialog('register_pop', 'Регистрация'); return false;" ><b class="text-danger">Зарегистрируйтесь</b></a>, пожалуйста, чтобы мы могли осуществлять для вас доставки.</i>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-12 map-form">
                    <div class="map-container">
                        <div class="map-info">
                            <span id="ShortInfo"/>
                            <div class="map-full-info" id="viewContainer"/>
                        </div>
                        <div id="map" style="width: 100%; min-height: 420px"/>
                    </div>
                </div>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>