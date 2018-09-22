<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <!--<xsl:include href="CSSnJS.header.xsl"/>-->
    <xsl:import href="CSSnJS.header.xsl"/>
    <xsl:template name="main_head">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <xsl:if test="//@fullscreen != 1">
                <meta name="viewport" content="width=device-width, initial-scale=1"/>
            </xsl:if>
            <base href="."/>
            <title>Скорая почта</title>
			<script>
				<![CDATA[
				function inIframe () {
					try {
						return window.self !== window.top;
					} catch (e) {
						return true;
					}
				}
				if (inIframe() && window.location.href.indexOf('without_menu') === -1) {
					window.top.location = window.location.href+'';
				}
				]]>
			</script>
			<link rel="stylesheet" href="/templates/yoo_capture/css/theme.css" />
            <xsl:call-template name="css_js_header"/>
			<script src="/templates/yoo_capture/warp/js/social.js"></script>
			<script src="/templates/yoo_capture/js/theme.js"></script>
			<script src="/templates/yoo_capture/js/parallax.js"></script>
        </head>
    </xsl:template>
    <xsl:template name="main_headWrap">
		<div class="tm-block tm-headerbar uk-clearfix  tm-slant-bottom">
			<div class="uk-container uk-container-center">
				<a class="uk-navbar-brand uk-hidden-small" href="/">
					<img src="/images/logo.png" width="256" height="40" alt="Скорая Почта"/>
				</a>
				<div class="uk-navbar-flip">
					<div class="uk-navbar-content uk-visible-large"><span class="code">+7 (812)</span> <span class="phone">242-80-81</span></div>
				</div>
				<div class="uk-navbar-flip uk-hidden-small">
					<ul class="uk-navbar-nav uk-hidden-small">
						<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
							<li class="dropdown">
								<a href="/pochta911/orders/"><i class="fa fa-flag" aria-hidden="true"> </i> Заказы</a>
							</li>
							<li class="dropdown">
								<a href="/pochta911/admin/userEdit-{/page/body/module[@name='CurentUser']/container/user_id}/"><i class="fa fa-user" aria-hidden="true"> </i> Карточка клиента</a>
							</li>
						</xsl:if>
						<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 or /page/body/module[@name='CurentUser']/container/group_id = 3 or /page/body/module[@name='CurentUser']/container/group_id = 4">
							<li>
								<a href="/pochta911/orders/LogistList-1/"><i class="fa fa-bus" aria-hidden="true"> </i> Логистика</a>
							</li>
						</xsl:if>
						<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 or /page/body/module[@name='CurentUser']/container/group_id = 3 or /page/body/module[@name='CurentUser']/container/group_id = 4">
							<li>
								<a href="/pochta911/admin/carsList-1/"><i class="fa fa-car" aria-hidden="true"> </i> Автоштат</a>
							</li>
						</xsl:if>
						<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 or /page/body/module[@name='CurentUser']/container/group_id = 4">
							<li>
								<a href="/pochta911/admin/userList-1/idg-2/"><span class="glyphicon glyphicon-user"> </span> Клиенты</a>
							</li>
						</xsl:if>
						<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
							<li>
								<a href="/pochta911/admin/userList-1/idg-0/"><span class="glyphicon glyphicon-user"> </span> Сотрудники</a>
							</li>
						</xsl:if>
						<li>
							<a href="/pochta911/?logout"><span class="glyphicon glyphicon-log-out"> </span> Выход</a>
						</li>
					</ul>
				</div>
				<div class="uk-navbar-brand uk-visible-small"><a class="tm-logo-small" href="https://pochta911.ru">
					<img src="/images/logo.png" width="256" height="40" alt="Скорая Почта"/></a></div>
				<span class="uk-navbar-toggle uk-navbar-flip uk-visible-small" data-uk-offcanvas="" onclick="$('#offcanvas').addClass('uk-active');"></span>
				<div id="offcanvas" class="uk-offcanvas uk-active2" aria-hidden="false" onclick="$(this).removeClass('uk-active');">
					<div class="uk-offcanvas-bar uk-offcanvas-bar-show" mode="push"><div class="uk-panel">
						<span class="code">+7 (812)</span> <span class="phone">242-80-81</span></div>
						<ul class="uk-nav uk-nav-offcanvas">
							<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
								<li class="dropdown">
									<a href="/pochta911/orders/"><i class="fa fa-flag" aria-hidden="true"> </i> Заказы</a>
								</li>
								<li class="dropdown">
									<a href="/pochta911/admin/userEdit-{/page/body/module[@name='CurentUser']/container/user_id}/"><i class="fa fa-user" aria-hidden="true"> </i> Карточка клиента</a>
								</li>
							</xsl:if>
							<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 or /page/body/module[@name='CurentUser']/container/group_id = 3 or /page/body/module[@name='CurentUser']/container/group_id = 4">
								<li>
									<a href="/pochta911/orders/LogistList-1/"><i class="fa fa-bus" aria-hidden="true"> </i> Логистика</a>
								</li>
							</xsl:if>
							<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 or /page/body/module[@name='CurentUser']/container/group_id = 3 or /page/body/module[@name='CurentUser']/container/group_id = 4">
								<li>
									<a href="/pochta911/admin/carsList-1/"><i class="fa fa-car" aria-hidden="true"> </i> Автоштат</a>
								</li>
							</xsl:if>
							<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 or /page/body/module[@name='CurentUser']/container/group_id = 4">
								<li>
									<a href="/pochta911/admin/userList-1/idg-2/"><span class="glyphicon glyphicon-user"> </span> Клиенты</a>
								</li>
							</xsl:if>
							<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
								<li>
									<a href="/pochta911/admin/userList-1/idg-0/"><span class="glyphicon glyphicon-user"> </span> Сотрудники</a>
								</li>
							</xsl:if>
							<li>
								<a href="/pochta911/?logout"><span class="glyphicon glyphicon-log-out"> </span> Выход</a>
							</li>
						</ul>
					</div>
				</div>
				<script>
					var now_path = window.location.pathname;
					$('ul li a[href="'+now_path+'"]').parent().addClass('uk-active');
				</script>
				<div class="tm-slant-block-bottom" style="border-right-width: 1048px; border-top-width: 26.2px; bottom: -25.2px;"></div>
				<div id="loading2" style="display:none;">
					<div class="loading-block">
						<p class="title" style="text-align:center;">Пожалуйста, подождите...
							<br/>
							<img src="images/anim_load.gif"/>
						</p>
					</div>
				</div>
			</div>
		</div>
    </xsl:template>
    <xsl:template name="bottom_block">
        <div id="foot">
            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                <div class="moduletable">
                    <ul class="bottom-menu navbar-nav">

                        <li>
                            <a class="btn btn-default btn-xs" href="/admin/">
                                <span class="glyphicon glyphicon-briefcase"> </span> Админ
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-default btn-xs" href="/admin/price_routes-1/">
                                <i class="fa fa-money" aria-hidden="true"> </i> Стоимость
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-default btn-xs" href="/admin/time_check_list-1/">
                                <i class="fa fa-clock-o" aria-hidden="true"> </i> Времена
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-default btn-xs" href="/admin/goods_price_list-1/">
                                <i class="fa fa-archive" aria-hidden="true"> </i> Товары
                            </a>
                        </li>
                    </ul>
                </div>
            </xsl:if>
        </div>
		<div style="text-align:center">
                <xsl:if test="//@fullscreen != 1">
                    <a href="?fullscreen=1">Полная версия</a>
                </xsl:if>
                <xsl:if test="//@fullscreen = 1">
                    <a href="?fullscreen=0">Адаптивная версия</a>
                </xsl:if>
            </div>
    </xsl:template>
</xsl:stylesheet>
