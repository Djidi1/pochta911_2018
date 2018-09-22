<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'useredit']">

        <h2>Профиль клиента:</h2>
        <xsl:if test="@add_data = 2">
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-exclamation-sign"/> Пожалуйста, установите новый пароль.
            </div>
        </xsl:if>
        <xsl:if test="@add_data = 1">
            <div class="alert alert-danger">
                <span class="glyphicon glyphicon-exclamation-sign"/> Прежде чем оставить заказ Вам необходимо заполнить всю информацию в своей карточке клиента.
            </div>
        </xsl:if>
        <form action="/admin/userUpdate-{user/user_id}/" method="post" name="main_form" autocomplete="off">
            <div class="row">
                <div class="col-md-6">

                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <strong>Контакты</strong>
                        </div>
                        <div class="panel-body">
                            <input id="user_id" type="hidden" name="user_id" value="{user/user_id}"/>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Название компании:</td>
                                        <td>
                                            <input class="form-control" type="text" name="title" onkeyup="check_user(this)"
                                                   value="{user/title}" size="30">
                                                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 1">
                                                    <xsl:attribute name="required">required</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Контактное лицо:</td>
                                        <td>
                                            <input class="form-control" type="text" name="username"
                                                   value="{user/name}" size="30">
                                                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 1">
                                                    <xsl:attribute name="required">required</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>E-mail:</td>
                                        <td>
                                            <input class="form-control" type="email" name="email" id="email"
                                                   value="{user/email}" size="30">
                                                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 1">
                                                    <xsl:attribute name="required">required</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Телефон:</td>
                                        <td>
                                            <input class="form-control" type="phone" name="phone"
                                                   value="{user/phone}" size="30">
                                                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 1">
                                                    <xsl:attribute name="required">required</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                        </td>
                                    </tr>
                                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                                        <tr>
                                            <td>Телеграм:</td>
                                            <td>
                                                <input class="form-control" type="text" name="phone_mess"
                                                       value="{user/phone_mess}" size="30">
                                                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 1">
                                                        <xsl:attribute name="required">required</xsl:attribute>
                                                    </xsl:if>
                                                </input>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Viber:</td>
                                            <td>
                                                <input class="form-control" type="text" name="viber_id"
                                                       value="{user/viber_id}" size="30">
                                                </input>
                                            </td>
                                        </tr>
                                    </xsl:if>
                                    <tr>
                                        <td>Логин:</td>
                                        <td>
                                            <input style="display:none" type="text" name="fakeusernameremembered"/>
                                            <input class="form-control" type="text" name="login" id="login" onkeyup="check_user(this)"
                                                   value="{user/login}" size="30">
                                                <xsl:if test="user/login != ''">
                                                    <xsl:attribute name="readonly">readonly</xsl:attribute>
                                                </xsl:if>
                                                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 1">
                                                    <xsl:attribute name="required">required</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Пароль:</td>
                                        <td>
                                            <input style="display:none" type="password" name="fakepasswordremembered"/>
                                            <xsl:if test="@add_data != 2">
                                                <span class="btn btn-sm btn-info" onclick="$(this).hide();$('#pass').show();$('#pass').focus()">Сменить пароль</span>
                                                <input style="display:none" class="form-control" type="password" name="pass" id="pass" autocomplete="new-password"/>
                                            </xsl:if>
                                            <xsl:if test="@add_data = 2">
                                                <input class="form-control" type="password" name="pass" id="pass" autocomplete="new-password" required=""/>
                                            </xsl:if>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Группа:</td>
                                        <td>
                                            <b>
                                                <xsl:for-each select="groups/item">
                                                    <xsl:if test="id = //user/group_id">
                                                        <xsl:value-of select="name"/>
                                                    </xsl:if>
                                                    <xsl:if test="id = //@group_id and not(//user/group_id)">
                                                        <xsl:value-of select="name"/>
                                                    </xsl:if>
                                                </xsl:for-each>
                                            </b>
                                            <select class="form-control" name="group_id" style="display:none">
                                                <xsl:for-each select="groups/item">
                                                    <option value="{id}">
                                                        <xsl:if test="id = //user/group_id">
                                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                                        </xsl:if>
                                                        <xsl:if test="id = //@group_id and not(//user/group_id)">
                                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                                        </xsl:if>
                                                        <xsl:value-of select="name"/>
                                                    </option>
                                                </xsl:for-each>
                                            </select>
                                        </td>
                                    </tr>
                                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                                        <tr>
                                            <td>Условия оплаты:</td>
                                            <td>
                                                <select class="form-control" name="pay_type">
                                                    <option value="0">cвободный выбор</option>
                                                    <xsl:for-each select="pay_types/item">
                                                        <option value="{id}">
                                                            <xsl:if test="id = //user/pay_type">
                                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:value-of select="pay_type"/>
                                                        </option>
                                                    </xsl:for-each>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Фиксированная стоимость по городу:</td>
                                            <td>
                                                <input class="form-control" type="text" name="fixprice_inside" id="fixprice_inside" value="{user/fixprice_inside}"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Максимальная стоимость по городу:</td>
                                            <td>
                                                <input class="form-control" type="text" name="maxprice_inside" id="maxprice_inside" value="{user/maxprice_inside}"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Процент инкассации:</td>
                                            <td>
                                                <input class="form-control" type="text" name="inkass_proc" id="inkass_proc" value="{user/inkass_proc}"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Отправлять СМС:</td>
                                            <td>
                                                <input type="hidden" name="send_sms" value="0"/>
                                                <input type="checkbox" name="send_sms" value="1" id="send_sms">
                                                    <xsl:if test="user/send_sms = 1">
                                                        <xsl:attribute name="checked"/>
                                                    </xsl:if>
                                                </input>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Заблокировать:</td>
                                            <td>
                                                <input type="hidden" name="isBan" value="0"/>
                                                <input type="checkbox" name="isBan" value="1" id="isBan"/>
                                            </td>
                                        </tr>
                                    </xsl:if>
                                </tbody>
                            </table>
                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 1">
                                <input class="form-control" type="hidden" name="send_sms" value="{user/send_sms}"/>
                                <input class="form-control" type="hidden" name="pay_type" value="{user/pay_type}"/>
                                <input class="form-control" type="hidden" name="phone_mess" value="{user/phone_mess}"/>
                                <input class="form-control" type="hidden" name="fixprice_inside" value="{user/fixprice_inside}"/>
                                <input class="form-control" type="hidden" name="inkass_proc" value="{user/inkass_proc}"/>
                            </xsl:if>
                            <!--<font color="red">* Поля обязательны для заполнения.</font>-->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <strong>Часы работы</strong>
                        </div>
                        <div class="panel-body">
                           <textarea name="work_times" class="form-control" placeholder="Будни: с 8 до 20&#10;Выходные: с 10 до 18">
                               <xsl:value-of select="user/work_times "/>
                           </textarea>
                        </div>
                    </div>
                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <strong>Условия расчетов с компанией</strong>
                            </div>
                            <div class="panel-body">
                                <textarea name="desc" class="form-control">
                                    <xsl:value-of select="user/desc "/>
                                </textarea>
                            </div>
                        </div>
                    </xsl:if>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <strong>Адреса магазинов</strong>
                        </div>
                        <div class="panel-body">
                            <xsl:for-each select="address/item">
                                <xsl:call-template name="address_row"/>
                            </xsl:for-each>
                            <xsl:if test="count(address/item) = 0">
                                <xsl:call-template name="address_row"/>
                            </xsl:if>
                        </div>
                    </div>

                    <div class="panel panel-warning">
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
                </div>
            </div>
            <div style="text-align: center">
                <input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
            </div>
        </form>
        <hr/>
    </xsl:template>

    <xsl:template name="address_row">
        <div class="input-group" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <input type="hidden" class="form-control" name="addr_id[]" value="{id}"/>
            <input type="text" class="form-control spb-streets" name="address[]" placeholder="Адрес" value="{address}"/>
            <br/>
            <textarea name="addr_comment[]" class="form-control" placeholder="Комментарий к адресу">
                <xsl:value-of select="comment"/>
            </textarea>
            <div class="input-group-btn" style="vertical-align: top;">
                <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row($(this).parent().parent())">
                    <xsl:if test="count(../../address/item) = 0 or position() != count(../../address/item)">
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
    <xsl:template name="pay_row">
        <div class="input-group" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <input type="text" class="form-control" name="credit_card[]" placeholder="Номер банковской карты" size="20" value="{card_num}"/>
            <br/>
            <textarea name="card_comment[]" class="form-control" placeholder="Комментарий к номеру карты">
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
