<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="//body/pageContent">
    <h3>Модуль</h3>
    <xsl:apply-templates select="items/item" />
  </xsl:template>

  <xsl:template match="items/actions">
    <div class="module-block">
        <table>
        <tbody>
            <tr>
                <td>ID</td>
                <td>Название модуля</td>
                <td>Действие</td>
                <td>Доступ</td>
                <td>Группа</td>
            </tr>
            <xsl:for-each select="action">
            <tr>
                <td><xsl:value-of select="id" /></td>
                <td><xsl:value-of select="mod_name" /></td>
                <td><xsl:value-of select="action_name" /></td>
                <td><xsl:value-of select="access" /></td>
                <td><xsl:value-of select="groups" /></td>
            </tr>
          </xsl:for-each>
        </tbody>
        </table>
    </div>
  </xsl:template>

</xsl:stylesheet>