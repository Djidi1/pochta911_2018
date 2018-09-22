<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="container[@module = 'errors']">   
  <div class="error-block">
    <xsl:for-each select="error">        
            <p class="title">
            <xsl:if test="@caption != ''"><xsl:value-of select="@caption"/><xsl:text> : </xsl:text></xsl:if><xsl:value-of select="."/></p>        
    </xsl:for-each>
    <a href="javascript:history.back(-1);">назад</a>
    </div>
  </xsl:template>
  	
</xsl:stylesheet>