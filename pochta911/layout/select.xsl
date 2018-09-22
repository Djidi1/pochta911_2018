<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- данный файл только для тестов и тестового проекта -->

<!-- основной шаблон для построения SELECT -->
<!--
  <select name="NAME" onChange="FUNC_NAME">
    <options><option value="xxx">dfddf</option></options>
    <scripts>SRC for JS</scripts>
  </select>
-->
  <xsl:template match="select">
    <xsl:apply-templates select="scripts"></xsl:apply-templates>
    <form>
      <xsl:attribute name="id">form_script</xsl:attribute>
      <xsl:attribute name="name">form_script</xsl:attribute>
      <input>
      <xsl:attribute name="name">data_type</xsl:attribute>
      <xsl:attribute name="value">xml</xsl:attribute>
      <xsl:attribute name="type">hidden</xsl:attribute>
      </input>
      <select>
          <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
          <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
          <xsl:attribute name="onChange"><xsl:value-of select="@onChange"/></xsl:attribute>
          <xsl:apply-templates select="options"></xsl:apply-templates>
      </select>
    </form>
  </xsl:template>

  <xsl:template match="options">
    <xsl:for-each select="option">
        <option>
            <xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
            <xsl:if test="@selected = 1"><xsl:attribute name="selected">@selected</xsl:attribute></xsl:if>
            <xsl:value-of select="." />
        </option>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="select/scripts">
    <script>
        <xsl:attribute name="src"><xsl:value-of select="src"/></xsl:attribute>
        <xsl:attribute name="language"><xsl:value-of select="@lang"/></xsl:attribute>
        <xsl:attribute name="type"><xsl:value-of select="@type"/></xsl:attribute>
    </script>
  </xsl:template>

</xsl:stylesheet>