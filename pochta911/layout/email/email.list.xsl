<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'mailform']">
		<div id="submit_result"></div>
		<form id="mailform" method="post" role="form" action="/email/send-1/">
		<div class="form-group">
			<b>От кого</b><br/>
			<select id="e_from" name="emailer_yourmail" class="multi form-control">
				<option value="7150611@mail.ru">7150611@mail.ru</option>
				<option value="mtk-mtk@mail.ru">mtk-mtk@mail.ru</option>
				<option value="djidi@mail.ru">djidi@mail.ru</option>
			</select>
		</div>
		<div class="form-group">
			<b>Кому</b><br/>
			<div class="btn-group" role="group">
				<label><input type="checkbox" id="checkbox_all" style="display:none" /><span class="btn btn-info btn-sm">Выбрать всех</span></label>&#160;
				<label><input type="checkbox" class="chkbx" style="display:none" rel="ku" /><span class="btn btn-info btn-sm">Купчино</span></label>&#160;
				<label><input type="checkbox" class="chkbx" style="display:none" rel="gr" /><span class="btn btn-info btn-sm">Гражданка</span></label>&#160;
				<label><input type="checkbox" class="chkbx" style="display:none" rel="vp" /><span class="btn btn-info btn-sm">Веселый</span></label>
			</div>
			<select id="emailer_mails" name="emailer_mails[]" class="multiselect form-control" multiple="multiple">
				<xsl:for-each select="items/item">
					<option value="{signup_email_address}" rel="{location}">
						<xsl:value-of select="location"/> | <xsl:value-of select="signup_email_address"/>
<xsl:if test="signup_username != 'NULL'"> - <xsl:value-of select="signup_username"/></xsl:if>
					</option>
				</xsl:for-each>
			</select>
		</div>
		<div class="form-group">
			<b>Сообщение</b><br/>
			<input type="text" name="emailer_subj" id="emailer_subj" placeholder="Тема письма"/>
			<textarea name="emailer_text" id="edit_content" placeholder="Текст письма"></textarea>
			<input id="submit_input" class="btn btn-info" type="input" value="" style="display:none; background: url('/images/loader.gif') no-repeat 1% 50% #3498db;" readonly="readonly"/>
			<input id="submit_button" class="btn btn-success" type="button" value="Отправить" onclick="email_submit();"/>
			<input id="reload_button" class="btn btn-primary" type="button" value="Отправить новые письма" onclick="location.reload();" style="display:none;" />
		</div>
		</form>
		<style>
		ul.select2-choices {max-height: 70px;overflow: auto !important;}
		</style>
		<script>
		$("#checkbox_all").click(function(){
			if($("#checkbox_all").is(':checked') ){
				$("#emailer_mails > option").prop("selected","selected");
				$("#emailer_mails").trigger("change");
			}else{
				$("#emailer_mails > option").removeAttr("selected");
				$("#emailer_mails").trigger("change");
			}
		});	
		$(".chkbx").click(function(){
			var loc = $(this).attr('rel');
			if($(this).is(':checked') ){
				$("#emailer_mails > option[rel='"+loc+"']").prop("selected","selected");
				$("#emailer_mails").trigger("change");
			}else{
				$("#emailer_mails > option[rel='"+loc+"']").removeAttr("selected");
				$("#emailer_mails").trigger("change");
			}
		});	
		</script>
	</xsl:template>
</xsl:stylesheet>
