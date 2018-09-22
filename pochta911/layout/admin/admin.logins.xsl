<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'admin/LoginsList-1']">
		<div>
			<div id="cpanel">
				<table class="adminform" align="center">
					<tbody>
						<tr>
							<td valign="top">
								<h2>Журнал входов</h2>
								<table cellpadding="3" cellspacing="1" border="0" width="100%">
									<tbody>
										<tr bgcolor="#B0C4DE">
											<th>Имя пользователя</th>
											<th>Группа</th>
											<th>IP</th>
											<th>Дата</th>
											<th>Откуда</th>
											<th>Чем входил</th>
											<th>ОС</th>
										</tr>
										<xsl:for-each select="item">
											<tr>
												<xsl:attribute name="bgcolor"><xsl:if test="position() mod 2 =1">#EDF7FE</xsl:if><xsl:if test="position() mod 2 =0">#E4F2FD</xsl:if></xsl:attribute>
												<td>
													<xsl:value-of select="name"/>
												</td>
												<td>
													<xsl:value-of select="group_name"/>
												</td>
												<td>
													<xsl:value-of select="ip"/>
												</td>
												<td>
													<xsl:value-of select="date"/>
												</td>
												<td>
													<xsl:value-of select="referer"/>
												</td>
												<td>
													<xsl:value-of select="browser"/>
												</td>
												<td>
													<xsl:value-of select="os"/>
												</td>
											</tr>
										</xsl:for-each>
									</tbody>
								</table>
								<xsl:call-template name="linkback"/>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<xsl:call-template name="archive"/>
	</xsl:template>
</xsl:stylesheet>
