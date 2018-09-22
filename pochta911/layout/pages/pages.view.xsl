<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="container[@module = 'pages']">
	  <div style="text-align:right;"><a href="#" onclick="printBlock('print_data')" class="btn btn-info glyphicon glyphicon-print" style="width: initial;margin-bottom: -50px;margin-right: 6px;"> </a></div>
	  <div id="print_data">
		<div class="panel panel-info">
			<div class="panel-heading"><h3 class="panel-title"><xsl:value-of select="item/title"/></h3></div>
			<div id="viewListlang" class="panel-body">
			<p class="description"><xsl:value-of select="item/description"/></p>
			<xsl:call-template name="pages-item" />   
			</div>
		</div>
	  </div>
  </xsl:template>
  
	<xsl:template match="container[@module = 'index']">
		<xsl:call-template name="pages-item" />
	</xsl:template>  
	
  <xsl:template match="container[@module = 'about']">
  <h2><xsl:value-of select="item/title"/></h2>
  <p class="description"><xsl:value-of select="item/description"/></p>
	  <xsl:call-template name="pages-item" />   
  </xsl:template>	

  <xsl:template match="container[@module = 'services']">
  <h2><xsl:value-of select="item/title"/></h2>
   <p class="description"><xsl:value-of select="item/description"/></p>
	  <xsl:call-template name="pages-item" />   
  </xsl:template>	

  <xsl:template match="container[@module = 'clients']">
  <h2><xsl:value-of select="item/title"/></h2>
   <p class="description"><xsl:value-of select="item/description"/></p>
	  <xsl:call-template name="pages-item" />   
  </xsl:template>
  
    <xsl:template match="container[@module = 'support']">
  <h2><xsl:value-of select="item/title"/></h2>
   <p class="description"><xsl:value-of select="item/description"/></p>
	  <xsl:call-template name="pages-item" />   
  </xsl:template>
  	
	<xsl:template name="pages-item"> 
	 <div><xsl:value-of select="item/content" disable-output-escaping="yes"/> </div>
  </xsl:template>

</xsl:stylesheet>