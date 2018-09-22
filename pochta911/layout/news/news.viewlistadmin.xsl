<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 
	<xsl:template match="container[@module = 'news']">
		<h2>Администрирование - новости</h2>
	<div class="row">
		<div class="col-md-2"><a href="http://{//page/@host}/news/new-1/" class="btn btn-success">Добавить</a></div>
		<div class="col-md-8">
		</div>		
		<div class="col-md-2"><a href="http://{//page/@host}/admin/" class="btn btn-info">Админка</a></div>
	</div>
		<xsl:call-template name="newsList" />
		
  </xsl:template>  
  
  <xsl:template name="newsList">
      
        <div >
        <table class="table table-striped table-hover table-condenced">
			<thead>
				<tr>
					<th>Дата</th>
					<th>Название</th>
					<th colspan="3"></th>
				</tr>
			</thead>
			<tbody>
			<xsl:for-each select="item">
				<tr>
					<td width="150" align="center"><a href="http://{//page/@host}/news/edit-{id}/"><xsl:value-of select="time"/></a></td>
					<td width="500">
					<a href="/news/view-{id}/"><xsl:value-of select="title" disable-output-escaping="yes"/></a>
					</td>
					<td><a href="http://{//page/@host}/news/edit-{id}/" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
					</td>
					<td><a href="/news/view-{id}/" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
					</td>
					<td>
					<a href="http://{//page/@host}/news/del-1/?news_id={id}" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
				</tr>
				</xsl:for-each>
			</tbody>
		</table>        
        </div>
      
   <xsl:call-template name="images" />
  </xsl:template>
	
	

</xsl:stylesheet>