<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'login']">
        <div class="form">
            <xsl:if test="login != 1">
                <xsl:call-template name="loginform"/>
            </xsl:if>
            <xsl:if test="login = 1">
                <xsl:call-template name="statusbar"/>
            </xsl:if>
        </div>
    </xsl:template>
    <xsl:template name="loginform">
        <div class="poping_links">
            <ul class="nav navbar-nav">
                <li class="dropdown" style="float:left;">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" onclick="showThem('login_pop');">
                        <i class="glyphicon glyphicon-user"/> Войти
                    </a>
                </li>
            </ul>
        </div>
    </xsl:template>
    <xsl:template name="statusbar">
        <ul class="nav navbar-nav">
            <li class="dropdown" style="float:left;">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="glyphicon glyphicon-user"/> <xsl:value-of select="error"/>
                    <i class="caret"/>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <!--<li>-->
                        <!--<xsl:value-of select="error"/>-->
                    <!--</li>-->
                    <li class="divider"/>
                    <li>
                        <a href="/?logout">
                            <i class="glyphicon glyphicon-log-out" title="Выход из системы"/>
                            Выход
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </xsl:template>
</xsl:stylesheet>
