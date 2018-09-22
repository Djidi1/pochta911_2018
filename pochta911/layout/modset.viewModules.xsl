<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'menu']">
		<xsl:apply-templates select="menuRoot"/>
	</xsl:template>
	<xsl:template match="container[@module = 'modset']">
		<xsl:apply-templates select="menuRoot"/>
		<xsl:apply-templates select="modulesList"/>
	</xsl:template>
	<xsl:template match="modulesList">
		<h3>Модули</h3>
		<div class="module-block">
			<table border="1" cellpadding="5" cellspacing="0">
				<tbody>
					<tr>
						<td>ID</td>
						<td>Название модуля</td>
						<td>
							<xsl:attribute name="rowspan">1</xsl:attribute>Действие</td>
						<td>
							<xsl:attribute name="rowspan">1</xsl:attribute>Доступ</td>
						<td>
							<xsl:attribute name="rowspan">1</xsl:attribute>Группа</td>
						<td>
							<xsl:attribute name="rowspan">1</xsl:attribute>Изменить</td>
					</tr>
					<xsl:for-each select="module">
						<xsl:variable name="codename">
							<xsl:value-of select="@id"/>
						</xsl:variable>
						<tr>
							<xsl:variable name="rowspan" select="count(actions/action)"/>
							<td>
								<xsl:attribute name="valign">top</xsl:attribute>
								<xsl:attribute name="rowspan"><xsl:value-of select="$rowspan + 2"/></xsl:attribute>
								<xsl:value-of select="@id"/>
							</td>
							<td>
								<xsl:attribute name="valign">top</xsl:attribute>
								<xsl:attribute name="rowspan"><xsl:value-of select="$rowspan + 2"/></xsl:attribute>
								<p><b><a href="/{@codename}/">
									<xsl:value-of select="@name"/></a>
								</b>
								</p>
								<p><xsl:value-of select="@codename"/></p>
							</td>
						</tr>
						<xsl:for-each select="actions/action">
							<tr>
								<td>
									<a href="/modset/usemod-{$codename}/useaction-{@action_name}/">
										<xsl:value-of select="@action_name"/>
									</a>
								</td>
								<td>
									<xsl:choose>
										<xsl:when test="@access = 1">
											<img alt="images/eye-green.gif" src="images/eye-green.gif" hspace="3"/>Открытое</xsl:when>
										<xsl:when test="@access = 2">
											<img alt="images/eye-yellow.gif" src="images/eye-yellow.gif" hspace="3"/>Регистрация</xsl:when>
										<xsl:when test="@access = 3">
											<img alt="images/eye-red.gif" src="images/eye-red.gif" hspace="3"/>Группа</xsl:when>
									</xsl:choose>
									:: <a href="/modset/action-chaccess/access-1/action_id-{@id}/">
										<img alt="images/eye-green.gif" src="images/eye-green.gif" hspace="3"/>
									</a>
									<a href="/modset/action-chaccess/access-2/action_id-{@id}/">
										<img alt="images/eye-yellow.gif" src="images/eye-yellow.gif" hspace="3"/>
									</a>
									<a href="/modset/action-chaccess/access-3/action_id-{@id}/">
										<img alt="images/eye-red.gif" src="images/eye-red.gif" hspace="3"/>
									</a>
								</td>
								<td>
									<xsl:value-of select="@groups_names"/>
								</td>
								<td>
									<a href="/modset/action-del/action_id-{@id}/">Удалить</a>
									<xsl:text> </xsl:text>
									<a href="#del">Изменить</a>
									<xsl:text> </xsl:text>
									<a href="#del">В группу</a>
								</td>
							</tr>
						</xsl:for-each>
						<tr>
							<td colspan="4" align="right">
								<a href="/modset/action-new/module_id-{$codename}" title="">Добавить</a>
							</td>
						</tr>
						<tr>
							<td colspan="6">
								<a href="#editmod">Редактировать</a>
								<xsl:text> </xsl:text>
								<a href="/modset/newChild-{@id}/">Дбавить потомка</a>
							</td>
						</tr>
						<tr>
							<td colspan="6">Добавочные модули</td>
						</tr>
						<tr>
							<td>ID</td>
							<td>codename</td>
							<td colspan="4">Название</td>
						</tr>
						<xsl:for-each select="addons/addon">
							<tr>
								<td><xsl:value-of select="id"/></td>
								<td><xsl:value-of select="codename"/></td>
								<td colspan="4"><xsl:value-of select="name"/></td>
							</tr>
						</xsl:for-each>
						<tr>
							<th bgcolor="#acacac" colspan="6">x</th>
						</tr>
					</xsl:for-each>
				</tbody>
			</table>
		</div>
	</xsl:template>
</xsl:stylesheet>
