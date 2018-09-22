<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="container[@module = 'news']">   
	<div class="panel panel-info">
		<div class="panel-heading"><h3 class="panel-title">Новости</h3></div>
		<div id="viewListlang" class="panel-body">
			<xsl:call-template name="single-news-item" />   
		</div>
	</div>
</xsl:template>
<xsl:template name="single-news-item">
	<xsl:for-each select="item">
		<div class="post_content_mass">
		   <div class="post_meta meta_type_line">
				<div class="post_meta_unite clearfix">
					<div class="post_date reply">
						<i class="icon-calendar"/>
						<time>
							<xsl:attribute name="datetime"><xsl:value-of select="time" /></xsl:attribute>
							<span class="label label-primary"><i class="fa fa-clock-o"/><xsl:text> </xsl:text><xsl:value-of select="time" /></span>
						</time>
					</div>
					<div class="post_author">
						
					</div>
				</div>
			</div>
		    <h4><xsl:value-of select="title" disable-output-escaping="yes"/></h4>
			<xsl:value-of select="content" disable-output-escaping="yes"/>
            <hr/>
			<xsl:value-of select="subject" disable-output-escaping="yes"/>
		</div>
	 </xsl:for-each>
</xsl:template>

</xsl:stylesheet>