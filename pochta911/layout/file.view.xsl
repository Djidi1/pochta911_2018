<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'images']">
		<xsl:call-template name="images"/>
	</xsl:template>
	<xsl:template match="container[@module = 'file']">
		<xsl:call-template name="images"/>
	</xsl:template>
	<xsl:template match="container[@module = 'fileGallery']">
		<xsl:call-template name="gallery"/>
	</xsl:template>
	<xsl:template name="images">
		<xsl:for-each select="image">
			<img>
				<xsl:if test="isImage = 'true'">
					<!--	<xsl:attribute name="src"><xsl:text>image/image-</xsl:text><xsl:value-of select="codename"/><xsl:text>.</xsl:text><xsl:value-of select="ext"/><xsl:text>/mime-</xsl:text><xsl:value-of select="mime" disable-output-escaping="yes"/><xsl:text>/fd-</xsl:text><xsl:value-of select="folder" disable-output-escaping="yes"/></xsl:attribute>
-->
					<xsl:attribute name="src">http://<xsl:value-of select="//page/@host"/>/index.php?module<xsl:text>=</xsl:text>image<xsl:text>&amp;</xsl:text>image<xsl:text>=</xsl:text><xsl:value-of select="codename"/><xsl:text>.</xsl:text><xsl:value-of select="ext"/><xsl:text>&amp;mime</xsl:text><xsl:text>=</xsl:text><xsl:value-of select="mime" disable-output-escaping="yes"/><xsl:text>&amp;fd</xsl:text><xsl:text>=</xsl:text><xsl:value-of select="folder" disable-output-escaping="yes"/></xsl:attribute>
				</xsl:if>
				<xsl:if test="isImage = 'false'">
					<xsl:attribute name="src"><xsl:text>images/file-img90x90.png</xsl:text></xsl:attribute>
				</xsl:if>
				<xsl:attribute name="alt"><xsl:value-of select="filename"/></xsl:attribute>
			</img>
		</xsl:for-each>
	</xsl:template>
	<xsl:template name="gallery">
		<xsl:for-each select="image">
			<img>
				<xsl:if test="isImage = 'true'">
					<xsl:attribute name="src"><xsl:text>/image/image-</xsl:text><xsl:value-of select="codename"/></xsl:attribute>
					<xsl:attribute name="width">70</xsl:attribute>
					<xsl:attribute name="height">70</xsl:attribute>
				</xsl:if>
				<xsl:if test="isImage = 'false'">
					<xsl:attribute name="src"><xsl:text>images/img90x90.png</xsl:text></xsl:attribute>
				</xsl:if>
				<xsl:attribute name="alt"><xsl:value-of select="filename"/></xsl:attribute>
			</img>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
