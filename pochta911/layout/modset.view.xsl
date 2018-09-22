<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="//body/pageContent">
    <h3>Модуль</h3>
    <xsl:apply-templates select="items/item" />
  </xsl:template>

  <xsl:template match="items/item">
    <div class="module-bloc">
        <table>
            <tr>
                <td>ID</td>
                <td>Название модуля</td>
                <td>Уникальное имя</td>
                <td>Class Process</td>
                <td>XSL</td>
                <td>действия</td>
            </tr>
            <tr>
                <td><xsl:value-off select="id" /></td>
                <td><xsl:value-off select="name" /></td>
                <td><xsl:value-off select="codename" /></td>
                <td><xsl:value-off select="processName" /></td>
                <td><xsl:value-off select="xsl" /></td>
            </tr>
            </table>
    </div>
  </xsl:template>

</xsl:stylesheet>