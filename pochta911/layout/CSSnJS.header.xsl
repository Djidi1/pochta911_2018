<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template name="css_js_header">
        <link rel="stylesheet" href="/pochta911/css/camera.css"/>
        <link rel="stylesheet" href="/pochta911/css/select2.css?v4.0.3"/>
        <link rel="stylesheet" href="/pochta911/css/style.css?v2.84"/>
        <link rel="stylesheet" href="/pochta911/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="/pochta911/css/print.css" media="Print"/>
        <link rel="stylesheet" href="/pochta911/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="/pochta911/css/bootstrap-datetimepicker.min.css"/>
        <link rel="stylesheet" href="/pochta911/css/dataTables.bootstrap.css"/>
        <link rel="stylesheet" href="/pochta911/css/animate.css"/>
        <script src="/pochta911/js/jquery.min.js?2.1.1"/>
        <script src="/pochta911/js/jquery-migrate.min.js"/>
        <script src="/pochta911/js/moment.min.js"/>
        <script src="/pochta911/js/moment.ru.js"/>
        <script src="/pochta911/js/bootstrap-datetimepicker.js"/>
        <xsl:if test="/page/@without_menu != 1">
            <link href="//cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css" rel="stylesheet" />
            <!--[if lt IE 10]>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js"></script>
            <![endif]-->
            <script src="//cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/js/jquery.suggestions.min.js"/>
            <script src="/pochta911/js/jquery-ui.min.js"/>
            <script src="/pochta911/js/bootstrap.min.js?3.3.1"/>
            <script src="/pochta911/js/bootbox.min.js"/>
            <script src="/pochta911/js/jquery.multiselect.min.js?v4.0.3"/>
            <script src="/pochta911/js/jquery.mask.js"/>
            <script src="/pochta911/js/bootstrap-typeahead.min.js"/>
            <script src="/pochta911/js/bootstrap-notify.min.js"/>
            <script src="/pochta911/js/camera.min.js"/>
            <script src="/pochta911/js/ready.js?v3.0.6"/>
            <script src="/pochta911/js/common.js?v3.0.14"/>
            <script src="/pochta911/js/script.js?v3.0.6"/>
            <xsl:if test="//page[@name='pages']">
                <script src="//cdn.ckeditor.com/4.6.1/full/ckeditor.js"/>
            </xsl:if>
            <script src="/pochta911/js/jquery.dataTables.min.js"/>
            <script src="/pochta911/js/dataTables.bootstrap.js"/>
            <xsl:text disable-output-escaping="yes">
                    <![CDATA[
                <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAnDrB-qO4i5uCua-4krGQsloWYJBRtgNU&libraries=places"></script>
                    ]]>
                </xsl:text>
            <script src="/pochta911/js/gmap.js?v3.0.9"/>
            <script>
                $(function(){
                    if ($('#edit_content').length){CKEDITOR.replace( 'edit_content');}
                });
            </script>

        </xsl:if>
    </xsl:template>

</xsl:stylesheet>