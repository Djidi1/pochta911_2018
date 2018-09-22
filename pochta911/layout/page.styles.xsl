<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

 <xsl:template match="header"> 
    <xsl:apply-templates select="meta" />
  </xsl:template>


  <xsl:template match="meta">
    <xsl:for-each select=".">
        <xsl:if test="@datatype = 'link'">
            <link>
                <xsl:attribute name="rel"><xsl:value-of select="@rel"/></xsl:attribute>
                <xsl:attribute name="type"><xsl:value-of select="@content"/></xsl:attribute>
                <xsl:attribute name="href"><xsl:value-of select="@type"/></xsl:attribute>
            </link>
        </xsl:if>
    
        <xsl:if test="@datatype = 'http-equiv'">
            <meta>
                <xsl:attribute name="http-equiv"><xsl:value-of select="@type"/></xsl:attribute>
                <xsl:attribute name="content"><xsl:value-of select="@content"/></xsl:attribute>
            </meta>
        </xsl:if>
        <xsl:if test="@datatype = 'name'">
            <meta>
                <xsl:attribute name="name"><xsl:value-of select="@datatype"/></xsl:attribute>
                <xsl:attribute name="content"><xsl:value-of select="@content"/></xsl:attribute>
            </meta>
        </xsl:if>
    </xsl:for-each>
  </xsl:template>


  <xsl:template match="head">
    <img>
        <xsl:attribute name="src"><xsl:value-of select="image/@src"/></xsl:attribute>
        <xsl:attribute name="alt"><xsl:value-of select="image/@alt"/></xsl:attribute>
        <xsl:attribute name="id"><xsl:value-of select="image/@id"/></xsl:attribute>
    </img>
  </xsl:template>

  <xsl:template match="pageContent">
    <xsl:apply-templates select="block"  />
  </xsl:template>

  <xsl:template match="news">
    <xsl:copy-of select="." />
  </xsl:template>

  <xsl:template match="footer">
    <xsl:copy-of select="."/>
  </xsl:template>

  <xsl:template match="menu">
    <xsl:copy-of select="."/>
  </xsl:template>
</xsl:stylesheet>