<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'emailedit']">
		<h2>Профиль подписчика:</h2>
		<form action="/email/emailEdit-{email/signups_id}" method="post" name="main_form">
			<div>
				<input type="hidden" name="signups_id" value="{email/signups_id}"/>
				<table>
					<tbody>
						<tr><td>Имя:</td><td><input type="text" name="signup_username" value="{email/signup_username}" size="30"/></td></tr>
						<tr><td>Email:</td><td><input type="text" name="signup_email_address" value="{email/signup_email_address}" size="30"/></td></tr>
						<tr><td>Район:</td><td>
							<select name="location">
								<option value="ku"><xsl:if test="email/location = 'ku'"><xsl:attribute name="selected"></xsl:attribute></xsl:if>Купчино</option>
								<option value="gr"><xsl:if test="email/location = 'gr'"><xsl:attribute name="selected"></xsl:attribute></xsl:if>Гражданка</option>
								<option value="vp"><xsl:if test="email/location = 'vp'"><xsl:attribute name="selected"></xsl:attribute></xsl:if>Веселый поселок</option>
							</select>
						</td></tr>
						<tr>
							<td></td>
							<td>
								<input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>		
	</xsl:template>
</xsl:stylesheet>
