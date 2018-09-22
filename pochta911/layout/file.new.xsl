<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="//news/items/form">
    <div class="file">
    <xsl:attribute name="class">file</xsl:attribute>
    <xsl:for-each select="input">
        <p>
        <input>
            <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
            <xsl:attribute name="type"><xsl:value-of select="@type"/></xsl:attribute>
            <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="value"><xsl:value-of select="."/></xsl:attribute>
            <xsl:if test="@selected = 1"><xsl:attribute name="selected">@selected</xsl:attribute></xsl:if>
        </input>
        </p>
    </xsl:for-each>
    </div>
  </xsl:template>

</xsl:stylesheet>