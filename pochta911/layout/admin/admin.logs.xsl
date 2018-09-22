<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="container[@module = 'logsfew']">
	<div>
		<h2>Журнал</h2>
		<table cellpadding="3" cellspacing="1" border="0" width="100%">
			<tbody>
				<tr bgcolor="#B0C4DE">
					<th>Сообщение</th>
					<th>Пользователь</th>
					<th>Дата</th>
					<th>IP</th>

				</tr>
				<xsl:for-each select="item">
				<tr><xsl:attribute name="bgcolor"><xsl:if test="position() mod 2 =1">#EDF7FE</xsl:if><xsl:if test="position() mod 2 =0">#E4F2FD</xsl:if></xsl:attribute>							
					<td><xsl:value-of select="text"/></td>
					<td><xsl:value-of select="username"/></td>
					<td><xsl:value-of select="date"/></td>
					<td><xsl:value-of select="ip"/></td>
				</tr>
				</xsl:for-each>
			</tbody>
		</table>
		<xsl:call-template name="linkback"/>
	</div>
</xsl:template>
</xsl:stylesheet>
