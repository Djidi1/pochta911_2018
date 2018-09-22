<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:template name="DDList">
		<xsl:param name="name_number" select="name_number" />
		<xsl:param name="onChange" />
		<xsl:param name="selectBy" select="@selectBy" />
		<select>
		<xsl:attribute name="selectBy"><xsl:value-of select="$selectBy"/></xsl:attribute>
		<xsl:attribute name="name">
			<xsl:value-of select="@name"/>
			<xsl:if test="$name_number !=''"><xsl:text>[</xsl:text><xsl:value-of select="$name_number"/><xsl:text>]</xsl:text></xsl:if>
		</xsl:attribute>
		<xsl:attribute name="id">
			<xsl:value-of select="@name"/>
			<xsl:if test="$name_number != ''"><xsl:text>_</xsl:text><xsl:value-of select="$name_number"/></xsl:if>
		</xsl:attribute>
		<xsl:attribute name="onChange">
			<xsl:if test="$onChange != ''"><xsl:value-of select="$onChange"/></xsl:if>
			<xsl:if test="onChangeJS != ''"><xsl:value-of select="onChangeJS"/></xsl:if>			
		</xsl:attribute>
			<xsl:for-each select="option">
				<option>
					<xsl:if test="id = $selectBy">
						<xsl:attribute name="selected"><xsl:text>selected</xsl:text></xsl:attribute>
					</xsl:if>
					<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
					<xsl:value-of select="name"/>
				</option>
			</xsl:for-each>
		</select>
	</xsl:template>
</xsl:stylesheet>
