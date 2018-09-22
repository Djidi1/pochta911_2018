<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'newuser']">
		<div>
		<div id="cpanel">
			<table class="adminform" align="center">
				<tbody>
					<tr>
						<td valign="top">
						
							<h2>Создать пользователя:</h2>
			<form action="http://{//page/@host}/admin/addUser-1/" method="post" style="width: 350px;">
				<table align="center">
					<tbody>
						<tr>
							<td>ФИО:</td>
							<td>
								<input type="text" name="username"/>
							</td>
						</tr>
						<tr>
							<td>E-mail:</td>
							<td>
								<input type="text" name="email"/>
							</td>
						</tr>
						<tr>
							<td>Телефон:</td>
							<td><input type="text" name="ip" /> </td>
						</tr>	
						<tr>
							<td>Название агента:</td>
							<td><input type="text" name="tab_no" id="tab_no" value="" size="30"/></td>
						</tr>						
						<tr>
							<td>Логин:</td>
							<td><input type="text" name="login" id="login"/> </td>
						</tr>
						<tr>
							<td>
								Пароль:
							</td>
							<td>
								<input type="password" name="pass" id="pass"/>
							</td>
						</tr>
						<tr>
							<td>Группа:</td>
							<td>
								<select name="group_id">
									<xsl:for-each select="groups/item">
										<option value="{id}">
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</td>
						</tr>
						<!--<tr>
							<td>Пароль автоматически:</td>
							<td><input type="hidden" name="isAutoPass" value="0" /><input type="checkbox" name="isAutoPass" value="1" id="isAutoPass"/> </td>
						</tr>	-->
						<tr>
							<td colspan="2">
								<input type="submit" value="создать" name="submit"/>
							</td>
						</tr>
					</tbody>
				</table>
				<font color="red">Все поля обязательны для заполнения.</font>
			</form>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
			
			<xsl:call-template name="linkback"/>
		</div>
	</xsl:template>
</xsl:stylesheet>
