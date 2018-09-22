<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="utf-8" indent="yes"/>
    <xsl:include href="head.main.page.xsl"/>
    <xsl:include href="head.page.xsl"/>
    <xsl:template match="/">
        <xsl:variable name="content">
            <xsl:value-of select="//page/body/@contentContainer"/>
        </xsl:variable>
        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html></xsl:text>
        <html>
            <xsl:call-template name="main_head"/>
            <body style="background: transparent" class="tm-isblog">
                <xsl:attribute name="id">
                    <xsl:value-of select="//page/@name"/>
                </xsl:attribute>
                <xsl:attribute name="user_name">
                    <xsl:value-of select="//page/body/module[@name='CurentUser']/container/user_name"/>
                </xsl:attribute>
                <xsl:attribute name="group_id">
                    <xsl:value-of select="//page/body/module[@name='CurentUser']/container/group_id"/>
                </xsl:attribute>
                <xsl:attribute name="user_id">
                    <xsl:value-of select="//page/body/module[@name='CurentUser']/container/user_id"/>
                </xsl:attribute>
                
                        <xsl:if test="/page/@without_menu != 1">
                            <xsl:if test="/page/body/module[@name='CurentUser']/container/login = 1">
                                <xsl:call-template name="main_headWrap"/>
                            </xsl:if>
                            <xsl:if test="not(/page/body/module[@name='CurentUser']/container/login) or /page/body/module[@name='CurentUser']/container/login != 1">
                                <xsl:call-template name="headWrap"/>
                            </xsl:if>
                        </xsl:if>
                        <div id="content">
                            <div class="wrapper2">
                                <xsl:choose>
                                    <xsl:when test="//page/body[@hasErrors = 0]">
                                        <xsl:apply-templates
                                                select="//page/body/module[@name = 'menu' and @name != '$content']"/>
                                        <xsl:apply-templates select="//page/body/module[@name = $content]"/>
                                    </xsl:when>
                                    <xsl:when test="//page/body[@hasErrors = 1]">
                                        <div id="errors">
                                            <h2>Ошибка</h2>
                                            <xsl:apply-templates select="//page/body/module[@name = 'error']"/>
                                            <xsl:apply-templates
                                                    select="//page/body/module[@name = $content]/container[@module = 'errors']"/>
                                            <p>
                                                <a href="/" title="На главную">На главную</a>
                                            </p>
                                        </div>
                                    </xsl:when>
                                    <xsl:when test="//page/body[@hasErrors = 2]">
                                        <xsl:apply-templates select="//page/body/module[@name = $content]"/>
                                    </xsl:when>
                                </xsl:choose>
                            </div>
                        </div>
                        <div class="clear"/>
                        <xsl:if test="/page/@without_menu != 1">
                            <xsl:call-template name="bottom_block"/>
                        </xsl:if>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
