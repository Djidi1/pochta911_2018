<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'grouprights']">
	<style type="">
	tr.selected {background-color: #EDEDED;}
	</style>
		<div>
			<div id="cpanel">
				<table class="adminform" width="100%">
					<tbody>
						<tr>
							<td valign="top">
								<h2>Настройка прав группы <xsl:value-of select="actions/@group_name"/>
								</h2>
								<p style="float:right;">
									<input type="button" name="admin" value="Обычный режим" onclick="show_rights(false)"/>
									<input type="button" name="admin" value="Детальный режим" onclick="show_rights(true)"/>
								</p>
								<div style="float: left;">
									<div class="icon">
										<a href="/admin/groupList-1/">
                                            <i class="fa fa-users" aria-hidden="true"> </i> Список групп
										</a>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<form action="/admin/groupRightsUpdate-1/" name="form" method="post">
				<input type="hidden" name="group_id" value="{actions/@group_id}"/>
				<table id="grouprights" class="table table-hover table-striped table-condensed">
					<tbody>
						<tr bgcolor="#B0C4DE">
							<th width="40">ID</th>
							<th colspan="2">Название</th>
							<th colspan="2">Наличие доступа</th>
						</tr>
						<xsl:for-each select="actions/module">
							<tr style="font-weight: bold;" id="mod{@mod_id}" onmouseout="this.className = 'darck';" onmouseover="this.className = 'selected';" class="darck">
								<xsl:attribute name="bgcolor"><xsl:if test="position() mod 2 =1">#EDF7FE</xsl:if><xsl:if test="position() mod 2 =0">#E4F2FD</xsl:if></xsl:attribute>
								<td valign="top" align="center">
									<!--<xsl:attribute name="rowspan"><xsl:value-of select="count(action)+1"/></xsl:attribute>-->
									<xsl:value-of select="@mod_id"/>
								</td>
								<td colspan="2">
									<xsl:value-of select="@mod_name"/>
								</td>
								<td/>
								<td align="center">
									<input id="set_{@mod_id}" onclick="set_chekbox({@mod_id});" type="checkbox">
										<xsl:if test="action[1]/inGroup = 1">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
								</td>
							</tr>
							<xsl:for-each select="action">
								<tr style="display:none;" class="more">
									<xsl:attribute name="bgcolor"><xsl:if test="position() mod 2 =1">#EDF7FE</xsl:if><xsl:if test="position() mod 2 =0">#E4F2FD</xsl:if></xsl:attribute>
									<td width="40">
									</td>
									<td width="40">
										<xsl:value-of select="id"/>
									</td>
									<td>
										<xsl:value-of select="action_title"/> (<xsl:value-of select="action_name"/>)</td>
									<td>
										<xsl:choose>
											<xsl:when test="access = 1">
                                                <span class="glyphicon glyphicon-eye-open text-success" aria-hidden="true"> </span> Открытое</xsl:when>
											<xsl:when test="access = 2">
                                                <span class="glyphicon glyphicon-eye-open text-warning" aria-hidden="true"> </span> Регистрация</xsl:when>
											<xsl:when test="access = 3">
                                                <span class="glyphicon glyphicon-eye-open text-danger" aria-hidden="true"> </span> Группа</xsl:when>
										</xsl:choose>
									</td>
									<td align="center">
										<input type="hidden" value="0">
											<xsl:attribute name="name">action[<xsl:value-of select="id"/>]</xsl:attribute>
										</input>
										<input type="checkbox" class="box_{../@mod_id}">
											<xsl:attribute name="value">1</xsl:attribute>
											<xsl:attribute name="name">action[<xsl:value-of select="id"/>]</xsl:attribute>
											<xsl:if test="inGroup = 1">
												<xsl:attribute name="checked">checked</xsl:attribute>
											</xsl:if>
										</input>
									</td>
								</tr>
							</xsl:for-each>
						</xsl:for-each>
					</tbody>
				</table>
				<p>
					<input type="submit" name="submit" value="сохранить"/>
				</p>
			</form>
		</div>
	</xsl:template>
</xsl:stylesheet>
