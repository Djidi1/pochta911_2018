<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="container[@module = 'grouprights']">
	<div>
		<h2>Группа: <xsl:value-of select="actions/@group_name" /></h2>
		<p><a href="http://{//page/@host}/admin/groupList-1/">К списку групп</a></p>
		<form action="http://{//page/@host}/admin/groupRightsUpdate-1/" name="form" method="post">
			<input type="hidden" name="group_id" value="{actions/@group_id}"/>
		
		<table cellpadding="3" cellspacing="1" border="0" width="100%">
			<tbody>
				<tr bgcolor="#B0C4DE"  >
					<th width="40">ID</th>
					<th colspan="4">Название</th>
					<th rowspan="1">*</th>
				</tr>
				<xsl:for-each select="actions/module">
				<tr style="font-weight: bold;" id="mod{@mod_id}"><xsl:attribute name="bgcolor"><xsl:if test="position() mod 2 =1">#EDF7FE</xsl:if><xsl:if test="position() mod 2 =0">#E4F2FD</xsl:if></xsl:attribute>							
					<td valign="top" align="center"> <xsl:attribute name="rowspan"><xsl:value-of select="count(action)+1"/></xsl:attribute>					
					<xsl:value-of select="@mod_id"/></td>
					<td colspan="2"><xsl:value-of select="@mod_name"/></td>					
					<td>Тип</td>					
					<td>Наличие</td>					
				</tr>
					<xsl:for-each select="action">
						<tr><xsl:attribute name="bgcolor"><xsl:if test="position() mod 2 =1">#EDF7FE</xsl:if><xsl:if test="position() mod 2 =0">#E4F2FD</xsl:if></xsl:attribute>
							
							<td width="40"><xsl:value-of select="id"/></td>
							<td><xsl:value-of select="action_title"/> (<xsl:value-of select="action_name"/>)</td>
							<td><xsl:choose>
										<xsl:when test="access = 1">
											<img alt="images/eye-green.gif" src="images/eye-green.gif" hspace="3"/>Открытое</xsl:when>
										<xsl:when test="access = 2">
											<img alt="images/eye-yellow.gif" src="images/eye-yellow.gif" hspace="3"/>Регистрация</xsl:when>
										<xsl:when test="access = 3">
											<img alt="images/eye-red.gif" src="images/eye-red.gif" hspace="3"/>Группа</xsl:when>
									</xsl:choose></td>
							<td>
							<input type="hidden" value="0"><xsl:attribute name="name">action[<xsl:value-of select="id"/>]</xsl:attribute></input>
								<input type="checkbox">
									<xsl:attribute name="value">1</xsl:attribute>
									<xsl:attribute name="name">action[<xsl:value-of select="id"/>]</xsl:attribute>
									<xsl:if test="inGroup = 1">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
							</td>
							<td>-</td>
						</tr>
					</xsl:for-each>
				</xsl:for-each>
			</tbody>
		</table>
		<p><input type="submit" name="submit" value="сохранить" /></p>
		</form>
		<xsl:call-template name="linkback"/>
	</div>

</xsl:template>
</xsl:stylesheet>
