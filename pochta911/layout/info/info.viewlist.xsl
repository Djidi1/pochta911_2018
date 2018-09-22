<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'info']">
		<div class="pages">
			 <div id="print_data">
				<div class="panel panel-info">
					<div class="panel-heading"><h3 class="panel-title">Полезная информация</h3></div>
					<div id="viewListlang" class="panel-body">
					<xsl:call-template name="pagesList"/>
					<!--<xsl:call-template name="archive"/>-->
					</div>
				</div>
			</div>
		</div>
	</xsl:template>
	<xsl:template name="pagesList">
		<table cellpadding="3" cellspacing="1" border="0" width="100%" class="table table-striped  table-condensed table-hover">
			<tbody>
				<xsl:for-each select="items/item">
					<tr>
						<td>
							<a href="/pages/view-{id}/">
								<xsl:value-of select="title"/>
							</a>
						</td>
						<td>
							<xsl:value-of select="description"/>
						</td>
						<td>
							<a href="/pages/view-{id}/" target="_blank" class="btn btn-info btn-xs">Просмотр</a>
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>
</xsl:stylesheet>
