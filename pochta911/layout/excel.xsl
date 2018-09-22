<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
	<xsl:template match="container[@module = 'viewreporttable']">
		<xsl:processing-instruction name="mso-application">
			<xsl:text>progid="Excel.Sheet"</xsl:text>
		</xsl:processing-instruction>
		<Workbook>
			<Styles>
				<Style ss:ID="Default" ss:Name="Normal">
					<Alignment ss:Vertical="Bottom"/>
					<Borders/>
					<Font/>
					<Interior/>
					<NumberFormat/>
					<Protection/>
				</Style>
			</Styles>
			<Worksheet ss:Name="Sheet 1">
				<Table>
					<Row>
						<Cell>
							<Data ss:Type="String">Last Name</Data>
						</Cell>
						<Cell>
							<Data ss:Type="String">First Name</Data>
						</Cell>
						<Cell>
							<Data ss:Type="String">Qty</Data>
						</Cell>
					</Row>
					<xsl:apply-templates select="//TestTable"/>
					<Row>
						<Cell/>
						<Cell>
							<Data ss:Type="String">Total:</Data>
						</Cell>
						<Cell>
							<xsl:attribute name="ss:Formula">=SUM(R[-<xsl:value-of select="count(/TestDataSet/TestTable)"></xsl:value-of>]C:R[-1]C)</xsl:attribute>
						</Cell>
					</Row>
				</Table>
			</Worksheet>
		</Workbook>
	</xsl:template>
	<xsl:template match="TestTable">
		<Row>
			<Cell>
				<Data ss:Type="String">
					<xsl:value-of select="LastName"/>
				</Data>
			</Cell>
			<Cell>
				<Data ss:Type="String">
					<xsl:value-of select="FirstName"/>
				</Data>
			</Cell>
			<Cell>
				<Data ss:Type="Number">
					<xsl:value-of select="Qty"/>
				</Data>
			</Cell>
		</Row>
	</xsl:template>
</xsl:stylesheet>
