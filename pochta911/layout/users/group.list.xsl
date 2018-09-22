<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'grouplist']">
		<style type="">
	tr.selected {background-color: #EDEDED;}
	</style>
		<div id="cpanel">
			<table class="adminform" width="100%">
				<tbody>
					<tr>
						<td valign="top">
							<h2>Список групп</h2>
							<div style="float: left;">
								<div class="icon">
									<a class="btn btn-success" href="http://{//page/@host}/admin/groupNew-1/">
										<i class="fa fa-users" aria-hidden="true"> </i>
										<span> Создать группу</span>
									</a>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div>
			<table cellpadding="3" cellspacing="1" border="0" width="100%">
				<tbody>
					<tr bgcolor="#B0C4DE">
						<th>ID</th>
						<th>Название</th>
						<th align="center" colspan="4">*</th>
					</tr>
					<xsl:for-each select="groups/item">
						<tr onmouseout="this.className = 'darck';" onmouseover="this.className = 'selected';" class="darck">
							<xsl:attribute name="bgcolor"><xsl:if test="position() mod 2 =1">#EDF7FE</xsl:if><xsl:if test="position() mod 2 =0">#E4F2FD</xsl:if></xsl:attribute>
							<td>
								<xsl:value-of select="id"/>
							</td>
							<td>
								<xsl:value-of select="name"/>
							</td>
							<td align="center" width="40px">
								<a class="btn btn-info btn-xs" href="http://{//page/@host}/admin/userList-1/idg-{id}/" title="список пользователей">
									<i class="fa fa-list" aria-hidden="true"> </i>
								</a></td>
							<td align="center" width="40px">
								<a class="btn btn-success btn-xs" href="http://{//page/@host}/admin/groupEdit-{id}/" title="редактировать">
									<i class="fa fa-pencil" aria-hidden="true"> </i>
								</a></td>
							<td align="center" width="40px">
								<a class="btn btn-warning btn-xs" href="http://{//page/@host}/admin/groupRightsAdmin-{id}/" title="права">
									<i class="fa fa-cog" aria-hidden="true"> </i>
								</a></td>
							<td align="center" width="40px">
								<a class="btn btn-danger btn-xs" href="http://{//page/@host}/admin/groupHide-{id}/" onclick="return confirm('Вы уверены?')" title="удалить">
									<i class="fa fa-ban" aria-hidden="true"> </i>
								</a>
							</td>
						</tr>
					</xsl:for-each>
				</tbody>
			</table>
		</div>
	</xsl:template>
</xsl:stylesheet>
