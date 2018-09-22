<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'adminmain']">
        <div id="cpanel">
            <table class="adminform" align="center" style="margin: 0 auto;">
                <tbody>
                    <tr>
                        <td valign="top">
                            <h2>Контент</h2>
                            <a class="btn btn-default" href="http://{//page/@host}/pages/">
                                <span class="glyphicon glyphicon-file"> </span>
                                <xsl:text> </xsl:text>
                                <span>Страницы</span>
                            </a>
                            <a class="btn btn-default" href="/news/newsadmin-1/">
                                <span class="glyphicon glyphicon-bullhorn"> </span>
                                <xsl:text> </xsl:text>
                                <span>Новости</span>
                            </a>

                            <a class="btn btn-default" href="/orders/">
                                <i class="fa fa-flag" aria-hidden="true"> </i>
                                <xsl:text> </xsl:text>
                                <span>Заказы</span>
                            </a>
                            <a class="btn btn-default" href="/orders/LogistList-1">
                                <i class="fa fa-bus" aria-hidden="true"> </i>
                                <xsl:text> </xsl:text>
                                <span>Логист</span>
                            </a>

                            <!--<div style="float: left;">-->
                            <!--<div class="icon">-->
                            <!--<a href="http://{//page/@host}/email/">-->
                            <!--<img src="/images/icon-48-mail.png" alt="Email рассылка"/>-->
                            <!--<span>Рассылка</span>-->
                            <!--</a>-->
                            <!--</div>-->
                            <!--</div>-->
                            <!--<div style="float: left;">-->
                            <!--<div class="icon">-->
                            <!--<a href="http://{//page/@host}/email/viewlist-1">-->
                            <!--<img src="/images/icon-48-mail.png" alt="Email подписчики"/>-->
                            <!--<span>Подписчики</span>-->
                            <!--</a>-->
                            <!--</div>-->
                            <!--</div>-->
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <h2>Настройки</h2>
                            <a class="btn btn-default" href="/admin/getTelegramUpdates-1/">
                                <i class="fa fa-telegram" aria-hidden="true"> </i>
                                <xsl:text> </xsl:text>
                                <span>Телеграмм</span>
                            </a>
                            <a class="btn btn-default" href="/admin/getViberUpdates-1/">
                                <i class="fa fa-phone" aria-hidden="true"> </i>
                                <xsl:text> </xsl:text>
                                <span>Viber</span>
                            </a>
                            <a class="btn btn-default" href="/admin/userList-1/">
                                <span class="glyphicon glyphicon-user"> </span>
                                <xsl:text> </xsl:text>
                                <span>Клиенты</span>
                            </a>
                            <a class="btn btn-default" href="/admin/carsList-1/">
                                <i class="fa fa-car" aria-hidden="true"> </i>
                                <xsl:text> </xsl:text>
                                <span>Автоштат</span>
                            </a>
                            <a class="btn btn-default" href="/admin/groupList-1/">
                                <i class="fa fa-users" aria-hidden="true"> </i>
                                <xsl:text> </xsl:text>
                                <span>Группы</span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <h2>Отчет</h2>
                            <form method="post" style="margin-bottom: 2px;" action="/admin/getReport-1/">
                                <div class="row">
                                    <div class="col-xs-9">
                                        <xsl:call-template name="datepickers"/>
                                    </div>
                                    <div class="col-xs-3">
                                        <input class="btn btn-success" type="submit" value="Выгрузить"/>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!--<xsl:call-template name="linkback"/>-->
        </div>
    </xsl:template>
    <xsl:template name="datepickers">
        <div class="input-daterange input-group" id="datepicker">
            <input type="text" class="form-control" id="start_date" name="date_from" value="{@date_from}" />
            <span class="input-group-addon">to</span>
            <input type="text" class="form-control" id="end_date" name="date_to" value="{@date_to}" />
        </div>
        <script type="text/javascript">
            $(function () {
            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

            $('#start_date').datetimepicker({format: 'L', locale: 'ru', defaultDate: firstDay,});
            $('#end_date').datetimepicker({format: 'L', locale: 'ru', useCurrent: false, defaultDate: lastDay});
            /*
            $("#start_date_excel").on("dp.change", function (e) {
            $('#end_date_excel').data("DateTimePicker").minDate(e.date);
            });
            $("#end_date_excel").on("dp.change", function (e) {
            $('#start_date_excel').data("DateTimePicker").maxDate(e.date);
            });
            $("#start_date_excel").on("dp.show", function (e) {
            $('#start_date_excel').data("DateTimePicker").maxDate(e.date);
            });
            $("#end_date_excel").on("dp.show", function (e) {
            $('#end_date_excel').data("DateTimePicker").minDate(e.date);
            });
            */
            });
        </script>
    </xsl:template>
</xsl:stylesheet>
