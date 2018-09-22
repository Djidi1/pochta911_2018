<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<!-- Это гостевой заголовок - ТИТУЛЬНАЯ СТРАНИЦА -->
	<xsl:include href="CSSnJS.header.xsl"/>
	<xsl:template name="head">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
			<xsl:if test="//@fullscreen != 1">
				<meta name="viewport" content="width=device-width, initial-scale=1"/>
			</xsl:if>
			<base href="."/>
			<title>Скорая почта</title>
			<xsl:call-template name="css_js_header"/>
			<script>
				function inIframe() {
					try {
						return window.self !== window.top;
					} catch (e) {
						return true;
					}
				}

				if (!inIframe()) {
					if (window.location.href.indexOf('login') > -1) {
						window.top.location = '/';
					}
				}
			</script>
		</head>
	</xsl:template>
	<xsl:template name="headWrap">
		<div id="header">
			<nav class="navbar navbar-default" style="display:none">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
								data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"/>
							<span class="icon-bar"/>
							<span class="icon-bar"/>
						</button>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<div class="moduletable_LoginForm navbar-right">
							<xsl:apply-templates select="//page/body/module[@name = 'CurentUser']/container[@module = 'login']"/>
						</div>
						<ul class="nav navbar-nav navbar-right">
							<li>
								<a href="#" onclick="showThem('register_pop', 'Регистрация'); return false;"><b class="text-danger">Регистрация</b></a>
							</li>
						</ul>
						<script>
							var now_path = window.location.pathname;
							$('ul li a[href="'+now_path+'"]').parent().addClass('active');
						</script>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
			
			<div class="mobile-sub-menu" style="display: none;">
				<div class="slogan">Мы спасаем ваше время</div>
				<div class="moduletable_LoginForm login-mobile">
					<xsl:apply-templates select="//page/body/module[@name = 'CurentUser']/container[@module = 'login']"/>
				</div>
				<div class="phone-in-header phone-mobile">
					<a href="tel:+7-812-242-80-81"><span class="city-code">(812)</span> 242-80-81</a>
				</div>
			</div>
		</div>
		<div id="loading2" style="display:none;"><div class="loading-block"><p class="title" style="text-align:center;">Пожалуйста, подождите...<br/><img src="images/anim_load.gif" /></p></div></div>
		
	</xsl:template>
</xsl:stylesheet>
