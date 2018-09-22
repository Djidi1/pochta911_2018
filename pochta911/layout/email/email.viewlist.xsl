<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'emaillist']">
		<xsl:if test="//page/@isAjax != 1">
		<hr/>
			<h2>Список email для рассылок</h2>
			<div align="right">
				<a href="#" onclick="open_dialog('/email/emailEdit-1/?emailEdit=-1&amp;ajax=1','Добавить Email',520,550); return false;" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Добавить</a>
			</div>
			<div id="viewListlang">
				<xsl:call-template name="viewTable"/>
			</div>
			<script>
			$(document).ready(function(){
				$('.sorted').DataTable({stateSave: true,
				language:{
			"sProcessing":   "Подождите...",
			"sLengthMenu":   "Показать _MENU_ записей",
			"sZeroRecords":  "Записи отсутствуют.",
			"sInfo":         "Записи с _START_ до _END_ из _TOTAL_ записей",
			"sInfoEmpty":    "Записи с 0 до 0 из 0 записей",
			"sInfoFiltered": "(отфильтровано из _MAX_ записей)",
			"sInfoPostFix":  "",
			"sSearch":       "Поиск:",
			"sUrl":          "",
			"oPaginate": {
				"sFirst": "Первая",
				"sPrevious": "Предыдущая",
				"sNext": "Следующая",
				"sLast": "Последняя"
			},
			"oAria": {
				"sSortAscending":  ": активировать для сортировки столбца по возрастанию",
				"sSortDescending": ": активировать для сортировки столбцов по убыванию"
			}
	   }});
			});
			</script>
		</xsl:if>
		<xsl:if test="//page/@isAjax = 1">
			<xsl:call-template name="viewTable"/>
		</xsl:if>
		
	</xsl:template>
	<xsl:template name="viewTable">
		<div>
			<form name="app_form" style="margin:0px" method="post" action="" id="printlist">
				<table class="table table-striped table-hover table-condensed sorted">
					<thead>
						<tr>
							<th>№ п/п</th>
							<th>Имя</th>
							<th>email</th>
							<th>Район</th>							
							<th>Дата подписки</th>							
							<xsl:if test="count(//page/@xls)=0">
								<th></th>
								<th></th>
							</xsl:if>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="items/item">
							<tr>
								<td><xsl:value-of select="position()"/></td>
								<td><xsl:value-of select="signup_username"/></td>
								<td><xsl:value-of select="signup_email_address"/></td>
								<td><xsl:value-of select="location"/></td>
								<td><xsl:value-of select="signup_date"/><xsl:text> </xsl:text><xsl:value-of select="signup_time"/></td>
								
								<td width="40px" align="center">
									<a href="#" onclick="open_dialog('/email/emailEdit-{signups_id}/','Редактировать Email',520,550); return false;" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
								</td>
								<td width="40px" align="center">
									<a href="/email/emailDelete-{signups_id}/" title="редактировать" onclick="return confirm('Вы уверены, что хотите удалить {signup_email_address}?') ? true : false;" class="btn btn-danger btn-sm">
										<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
									</a>
								</td>
							</tr>
						</xsl:for-each>
					</tbody>
				</table>
			</form>
		</div>
	</xsl:template>
</xsl:stylesheet>
