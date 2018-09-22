<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="utf-8" indent="yes"/>
    <xsl:template match="/">
        <xsl:variable name="content">
            <xsl:value-of select="//page/body/@contentContainer"/>
        </xsl:variable>
        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html></xsl:text>
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <link rel="stylesheet" href="/css/bootstrap.min.css"/>
                <link rel="stylesheet" href="/css/style.css?v2.6"/>
                <script>
                    function print_window() {
                        setTimeout(function () {
                            window.print();
                        }, 500);
                        setTimeout(function () {
                            window.close();
                        }, 500);
                    }
                </script>
            </head>
            <body onload='print_window();'>
                <xsl:apply-templates select="//page/body/module[@name = $content]"/>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
