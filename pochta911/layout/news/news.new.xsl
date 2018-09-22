<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="//news/items/form">
    <form>
      <xsl:attribute name="ENCTYPE">multipart/form-data</xsl:attribute>
      <xsl:attribute name="name">form_script</xsl:attribute>
      <xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
      <xsl:attribute name="method"><xsl:value-of select="@method"/></xsl:attribute>
    <table>
    <xsl:for-each select="item">
       <tr>
       <xsl:if test="@type != 'textarea'">
        <td><xsl:value-of select="."/></td>
       </xsl:if>

       <xsl:if test="@type = 'textarea'">
        <td><xsl:value-of select="@title"/></td>
       </xsl:if>

        <td>
        <xsl:if test=". = ''">
            <xsl:attribute name="colspan"><xsl:value-of select="2"/></xsl:attribute>
       </xsl:if>
         <xsl:if test="@type != 'textarea'">
          <input>
              <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
              <xsl:attribute name="type"><xsl:value-of select="@type"/></xsl:attribute>
              <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
              <xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
              <xsl:if test="@selected = 1"><xsl:attribute name="selected">@selected</xsl:attribute></xsl:if>
          </input>
          </xsl:if>
         <xsl:if test="@type = 'textarea'">
          <textarea class="ckeditor content_edit">
              <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
              <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
              <xsl:value-of select="."/>
          </textarea>
          </xsl:if>
        </td>
        </tr>
    </xsl:for-each>
    </table>
    </form>
  </xsl:template>

</xsl:stylesheet>