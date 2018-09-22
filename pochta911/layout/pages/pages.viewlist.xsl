<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'pages']">
		<div class="pages">
			<h3 style="color:white;">
				Страницы
			</h3>
			<a href="/pages/new-1/" class="btn btn-success btn-large">Добавить страницу</a>
			<hr/>
			<xsl:call-template name="pagesList"/>
			<!--<xsl:call-template name="archive"/>-->
		</div>
	</xsl:template>
	<xsl:template name="pagesList">
		<table cellpadding="3" cellspacing="1" border="0" width="100%" class="table table-striped  table-hover">
			<tbody>
				<tr bgcolor="#B0C4DE">
					<th width="50">id</th>
					<th width="80%">Название</th>
					<!--<th>NEW</th>-->
					<th colspan="2" width="100"/>
				</tr>
				<xsl:for-each select="items/item">
					<tr>
						<td>
							<xsl:value-of select="id"/>
						</td>
						<td>
							<xsl:if test="module=2"><span class="hint btn-default btn-xs">sys</span></xsl:if>
							<xsl:if test="module=11"><span class="hint btn-success btn-xs">tur</span></xsl:if>
							<xsl:if test="module=13"><span class="hint btn-info btn-xs">info</span></xsl:if>
							<xsl:value-of select="title"/>
						</td>
						<!--<td>-->
							<!--<xsl:value-of select="access"/>-->
						<!--</td>-->
						<td>
							<a href="/pages/view-{id}/" target="_blank" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"/> Просмотр</a>
						</td>
						<td>
							<a href="/pages/edit-{id}/" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"/> Редактировать</a>
						</td>
						<!--<td>-->
							<!--<a href="/pages/del-{id}/" class="btn btn-danger btn-xs" title="Удалить">-->
											<!--<xsl:attribute name="onClick">if (confirm("Вы действительно хотите удалить эту страницу?")) {return true;} else {return false;}</xsl:attribute><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>-->
						<!--</td>-->
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>
</xsl:stylesheet>
