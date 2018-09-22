<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'caredit']">

        <h2>Курьер/автомобиль:</h2>
        <form action="/admin/carUpdate-{car/id}/" method="post" name="main_form">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-5">

                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <strong>Контакты / Характеристики</strong>
                        </div>
                        <div class="panel-body">
                            <input type="hidden" name="car_id" value="{car/id}"/>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>ФИО:</td>
                                        <td>
                                            <input class="form-control" type="text" name="fio"
                                                   value="{car/fio}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Телефон:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="phone"
                                                   value="{car/phone}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Телефон (доп.):</td>
                                        <td>
                                            <input class="form-control" type="phone" name="phone2"
                                                   value="{car/phone2}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>E-mail:</td>
                                        <td>
                                            <input class="form-control" type="email" name="email" id="email"
                                                   value="{car/email}" size="30">
                                            </input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Телеграмм:</td>
                                        <td>
                                            <input class="form-control" type="text" name="telegram"
                                                   value="{car/telegram}" size="30" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Viber:</td>
                                        <td>
                                            <input class="form-control" type="text" name="viber"
                                                   value="{car/viber}" size="30" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Марка:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="car_firm"
                                                   value="{car/car_firm}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Номер:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="car_number"
                                                   value="{car/car_number}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Год:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="car_year"
                                                   value="{car/car_year}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Объем:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="car_value"
                                                   value="{car/car_value}" size="30" required=""/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Тип авто:</td>
                                        <td>
                                            <select class="form-control" name="car_type">
                                                <xsl:for-each select="car_types/item">
                                                    <option value="{id}">
                                                        <xsl:if test="id = //car/car_type">
                                                            <xsl:attribute name="selected">selected
                                                            </xsl:attribute>
                                                        </xsl:if>
                                                        <xsl:value-of select="car_type"/>
                                                    </option>
                                                </xsl:for-each>
                                            </select>
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                    <!--<td>Заблокировать:</td>-->
                                    <!--<td>-->
                                    <!--<input type="hidden" name="isBan" value="0"/>-->
                                    <!--<input type="checkbox" name="isBan" value="1" id="isBan"/>-->
                                    <!--</td>-->
                                    <!--</tr>-->
                                </tbody>
                            </table>
                            <!--<font color="red">* Поля обязательны для заполнения.</font>-->
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <strong>Оплата</strong>
                        </div>
                        <div class="panel-body">
                            <xsl:for-each select="cards/item">
                                <xsl:call-template name="pay_row"/>
                            </xsl:for-each>
                            <xsl:if test="count(cards/item) = 0">
                                <xsl:call-template name="pay_row"/>
                            </xsl:if>
                        </div>
                </div>

                    <!--<div class="panel panel-info">-->
                        <!--<div class="panel-heading">-->
                            <!--<strong>История заказов</strong>-->
                        <!--</div>-->
                        <!--<div class="panel-body">-->
                            <!--<xsl:for-each select="orders/item">-->

                            <!--</xsl:for-each>-->
                            <!--<xsl:if test="count(orders/item) = 0">-->
                                <!--Заказов у данного курьера не обнаружено.-->
                            <!--</xsl:if>-->
                        <!--</div>-->
                    <!--</div>-->

                </div>
            </div>
            <div style="text-align: center">
                <input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
            </div>
        </form>

    </xsl:template>
    <xsl:template name="pay_row">
        <div class="input-group" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <input type="text" class="form-control" name="credit_card[]" placeholder="Номер карты Сбербанка" size="20" value="{card_num}"/>
            <br/>
            <textarea name="card_comment[]" class="form-control">
                <xsl:value-of select="comment"/>
            </textarea>
            <div class="input-group-btn" style="vertical-align: top;">
                <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row($(this).parent().parent())">
                    <xsl:if test="count(../../cards/item) = 0 or position() != count(../../cards/item)">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    +
                </button>
                <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)">
                    <xsl:if test="position() = 1">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    -
                </button>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
