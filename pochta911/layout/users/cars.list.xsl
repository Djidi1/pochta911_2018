<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'carslist']">
        <div id="cpanel">
            <table class="adminform" width="100%">
                <tbody>
                    <tr>
                        <td valign="top">
                            <h2>Список курьеров/машин</h2>
                            <div style="float: left;">
                                <div class="icon">
                                    <a class="btn btn-success" href="/admin/carEdit-0/" title="Добавить курьера">
                                        <span class="glyphicon glyphicon-car"> </span>
                                        <span>Добавить курьера</span>
                                    </a>
                                </div>
                            </div>
                            <!--<div style="float: right;">-->
                                <!--<input class="btn btn-info btn-sm" type="button" onclick="printBlock('#printlist');" value="Печать"/>-->
                                <!--<input class="btn btn-info btn-sm" type="button" onclick="buttonSetFilter('langFilter', '1', 'ajax','input', '/admin/userList-1/xls-1/', true)" value="Excel"/>-->
                            <!--</div>-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <hr/>
        <div id="printlist">
            <table class="table table-hover table-stripsed table-condensed data-table">
                <thead>
                    <tr>
                        <th>№ [ID]</th>
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>Viber</th>
                        <th>Email</th>
                        <th>Тип машины</th>
                        <th>Марка</th>
                        <th>Год</th>
                        <th>Номер</th>
                        <th>Объем</th>
                        <xsl:if test="count(//page/@xls)=0">
                            <th> </th>
                            <th> </th>
                        </xsl:if>
                    </tr>
                </thead>
                <tbody>
                    <xsl:for-each select="cars/car">
                        <tr>
                            <td>
                                <xsl:value-of select="position()"/> [<xsl:value-of select="id"/>]
                            </td>
                            <td><xsl:value-of select="fio"/></td>
                            <td><xsl:value-of select="phone"/> <xsl:if test="phone != phone2">/ <xsl:value-of select="phone2"/></xsl:if></td>
                            <td><xsl:value-of select="viber"/></td>
                            <td><xsl:value-of select="email"/></td>
                            <td><xsl:value-of select="car_type"/></td>
                            <td><xsl:value-of select="car_firm"/></td>
                            <td><xsl:value-of select="car_year"/></td>
                            <td><xsl:value-of select="car_number"/></td>
                            <td><xsl:value-of select="car_value"/></td>

                            <xsl:if test="count(//page/@xls)=0">
                                <td width="40px" align="center">
                                    <a href="/admin/carEdit-{id}/" title="редактировать" class="btn btn-success btn-xs">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>
                                    </a>
                                </td>
                                <td width="40px" align="center">
                                    <a href="/admin/carBan-{id}/" title="удалить" class="btn btn-danger btn-xs">
                                        <xsl:attribute name="onClick">return confirm('Вы действительно хотите удалить курьера <xsl:value-of select="fio"/>?');
                                        </xsl:attribute>
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>
                                    </a>
                                </td>
                            </xsl:if>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </div>
    </xsl:template>
</xsl:stylesheet>
