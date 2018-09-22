<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'news']">
		<div class="news">
		
			<h2>
				<a href="http://{//page/@host}/news/">
					<xsl:call-template name="word">
						<xsl:with-param name="k">news_key</xsl:with-param>
					</xsl:call-template>
				</a>
			</h2>
			<xsl:call-template name="newsList"/>
			<xsl:call-template name="archive"/>
		</div>
	</xsl:template>
	<xsl:template match="container[@module = 'newsFooter']">
		<div class="title-box clearfix home-title3">
			<h2 class="title-box clearfix home-title3">
				<a href="http://{//page/@host}/news/">
					<xsl:call-template name="word">
						<xsl:with-param name="k">news_key</xsl:with-param>
					</xsl:call-template>
				</a>
			</h2>
		</div>
		<div class="posts-list home-list2">
			<xsl:call-template name="newsListIndex"/>
		</div>
		<div class="news">
			<p class="more">
				<a class="btn" href="http://{//page/@host}/news/">
					<xsl:call-template name="word">
						<xsl:with-param name="k">all_news_key</xsl:with-param>
					</xsl:call-template>
				</a>
			</p>
		</div>
	</xsl:template>
	<!-- НОВОСТИ НА ГЛАВНОЙ -->
	<xsl:template name="newsListIndex">
		<div class="row-fluid" >
			<article class="span12 post__holder">
				<xsl:for-each select="item">
					
					<table border="0" style="border-bottom:1px solid #DDD;">
					<tr>
						<td> 
							<header class="post-header" style="clear: both;">
								<span class="post_date">
								<time>
									<xsl:attribute name="datetime"><xsl:value-of select="time"/></xsl:attribute>
									<xsl:value-of select="time"/>
								</time>
								</span>
							</header>
						</td>
						<td rowspan="2" valign="top">
						<div class="post_content" >
								<span style=" line-height: 16px; font-size: 12px;">
									<xsl:value-of select="title"/>
								</span>
							</div> </td>
					</tr>
					<tr>
						<td >
							<figure class="featured-thumbnail thumbnail " style="width:80px;height:40px;">
								<a class="news_pic" title="" href="/news/{year}/{month}/{day}/{id}/">
									<xsl:apply-templates select="module[@name = 'file']"/>
								</a>
							</figure>
						</td>
						
					</tr>
					</table>
				</xsl:for-each>
			</article>
		</div>
	</xsl:template>
	<xsl:template name="newsList">
	<div class="comment-holder">
				<ol class="comment-list clearfix">
		<xsl:for-each select="item">
			<li class="newsList byuser comment-author-admin bypostauthor even thread-even depth-1 clearfix">
					<div class="comment-body clearfix" style=" height: 100px; overflow:hidden;">
						<div class="wrapper">
							<div class="comment-meta commentmetadata">
								<time>
										<xsl:attribute name="datetime"><xsl:value-of select="time"/></xsl:attribute>
										<xsl:value-of select="time"/>
									</time>
							</div>
							
						</div>
						<div class="wrapper">
							<div class="comment-author vcard">
								<span class="author">
								<xsl:apply-templates select="module[@name = 'file']"/>
								</span>
							</div>
							<div class="extra-wrap">
								<b><xsl:value-of select="title"/></b>
							</div>
						</div>
						<div class="reply">
								<a class="comment-reply-link" href="/news/{year}/{month}/{day}/{id}/">Подробнее</a>
							</div>
					</div>
				
			</li>
		</xsl:for-each>
		</ol>
			</div>
	</xsl:template>
</xsl:stylesheet>
