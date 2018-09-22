<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:template match="menuRoot">
		<xsl:variable name="selected" select="@selected"/>
		<div class="module-menu">
			<h3>Меню
			</h3>
			<ul>
				<xsl:apply-templates select="modeset_menuItem">
					<xsl:with-param name="selected">$selected</xsl:with-param>
				</xsl:apply-templates>
			</ul>
		</div>
	</xsl:template>
	<xsl:template match="modeset_menuItem">
		<xsl:param name="selected">$selected</xsl:param>
		<li>
			<xsl:if test="codename = $selected">
				<xsl:attribute name="id">active</xsl:attribute>
			</xsl:if>
			<xsl:if test="parentMod != 0">
				<xsl:attribute name="class">parent</xsl:attribute>
			</xsl:if>
			<a href="/{@href}/" title="{@title}">
				<xsl:value-of select="name"/>
			</a>
			<xsl:if test="count(menuItem) != 0">
			<ul>
				<xsl:apply-templates select="modeset_menuItem">
					<xsl:with-param name="selected">$selected</xsl:with-param>
				</xsl:apply-templates>
			</ul>
			</xsl:if>
		</li>
	</xsl:template>
</xsl:stylesheet>
