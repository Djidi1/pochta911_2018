<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/TR/xhtml1/strict">
	<xsl:output method="html" omit-xml-declaration="yes" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" indent="yes"/>
	<xsl:include href="head.page.xsl"/>
	<xsl:template match="/">
		<xsl:variable name="content">
			<xsl:value-of select="//page/body/@contentContainer"/>
		</xsl:variable>
		<html xmlns="http://www.w3.org/1999/xhtml">
			<xsl:call-template name="head"/>
			<body>
				<xsl:attribute name="id"><xsl:value-of select="//page/@name"/></xsl:attribute>
				<div class="wrap">
					<xsl:call-template name="headWrap"/>
					<div class="contentWrap">
						<div class="content-pad">
							<div id="content">
								<!--<div id="content-right">-->
								<div class="content-left">
									<xsl:choose>
										<xsl:when test="//page/body[@hasErrors = 0]">
											<xsl:apply-templates select="//page/body/module[@name = $content]"/>
										</xsl:when>
										<xsl:when test="//page/body[@hasErrors = 1]">
											<div id="errors">
												<h2>Ошибка</h2>
												<xsl:apply-templates select="//page/body/module[@name = 'error']"/>
												<xsl:apply-templates select="//page/body/module[@name = $content]/container[@module = 'errors']"/>
												<xsl:apply-templates select="//page/body/module[@name = $content]/container[@module = 'login']"/>
												<p>
													<a href="/" title="На главную">На главную</a>
												</p>
											</div>
										</xsl:when>
										<xsl:when test="//page/body[@hasErrors = 2]">
											<xsl:apply-templates select="//page/body/module[@name = $content]"/>
										</xsl:when>
									</xsl:choose>
								</div>
								<div class="content-right">
									<div class="products">
										<xsl:apply-templates select="//page/body/module[@name = 'productsIndex']"/>
									</div>
								</div>
								<!--   </div>  content-right -->
								<!--	<div id="content-left"></div>    content-left -->
							</div>
							<!-- content -->
						</div>
						<!-- content-pad -->
					</div>
					<!-- contentWrap -->
					<xsl:call-template name="infoBlock"/>
					<!-- footer -->
				</div>
				<!-- wrap -->
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
