<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'excel']">
        <form method="post" style="margin-bottom: 2px;" action="?logist={@logist}">
            <div class="row">
                <div class="col-xs-9">
                    <xsl:call-template name="datepickers"/>
                </div>
                <div class="col-xs-3">
                    <input class="btn btn-success" type="submit" name="sub_action" value="excel"/>
                </div>
            </div>
        </form>
    </xsl:template>
    <xsl:template name="datepickers">
        <div class="input-daterange input-group" id="datepicker">
            <input type="text" class="form-control" id="start_date" name="date_from" value="{@date_from}" />
            <span class="input-group-addon">to</span>
            <input type="text" class="form-control" id="end_date" name="date_to" value="{@date_to}" />
        </div>
        <script type="text/javascript">
            $(function () {
                $('#start_date').datetimepicker({format: 'L', locale: 'ru'});
                $('#end_date').datetimepicker({format: 'L', locale: 'ru', useCurrent: false});
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