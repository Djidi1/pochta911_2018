<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'priceslist']">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#price" aria-controls="price" role="tab" data-toggle="tab">Настройка условий</a></li>
			<li role="presentation"><a href="#goods" aria-controls="goods" role="tab" data-toggle="tab">Список типов товаров</a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="price">
				<h2>Настройка стоимости за перевозку товаров</h2>
				<form method="post" class="time_check_list">
					<input type="hidden" name="sub_action" value="save"/>

					<table class="table table-hover table-striped">
						<tr>
							<th>Товар</th>
							<th>Условие</th>
							<th>Количество</th>
							<th>Стоимость</th>
							<th>Фиксированная</th>
							<th>Множитель</th>
						</tr>
						<xsl:for-each select="item">
							<tr>
								<td>
									<xsl:value-of select="goods_name"/>
									<input type="hidden" name="id[]" value="{id}"/>
								</td>
								<td>
									<select name="condition[{id}]" class="form-control">
										<option value="&gt;"><xsl:if test="condition = '&gt;'"><xsl:attribute name="selected"/></xsl:if>Больше</option>
										<option value="&lt;"><xsl:if test="condition = '&lt;'"><xsl:attribute name="selected"/></xsl:if>Меньше</option>
										<option value="="><xsl:if test="condition = '='"><xsl:attribute name="selected"/></xsl:if>Равно</option>
									</select>
								</td>
								<td>
									<input name="value[{id}]" class="form-control" type="number" value="{value}"/>
								</td>
								<td>
									<input name="price[{id}]" class="form-control" type="number" value="{price}"/>
								</td>
								<td>
									<div class="funkyradio">
										<div class="funkyradio-success">
											<input type="checkbox" id="checkbox_{position()}" name="fixed[{id}]" value="1" >
												<xsl:if test="fixed = 1">
													<xsl:attribute name="checked"/>
												</xsl:if>
											</input>
											<label for="checkbox_{position()}" style="width: 30px;"/>
										</div>
									</div>
								</td>
								<td>
									<input name="mult[{id}]" class="form-control" type="number" value="{mult}"/>
								</td>
							</tr>
						</xsl:for-each>
					</table>
					<input class="btn btn-success" type="submit" value="Сохранить условия"/>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="goods">
				<h2>Названия товаров</h2>
				<form method="post" class="">
					<input type="hidden" name="sub_action" value="save_goods"/>
					<table class="table table-hover table-striped">
						<tr>
							<th>id</th>
							<th>Название</th>
						</tr>
						<xsl:for-each select="goods/item">
							<tr>
								<td>
									<xsl:value-of select="id"/>
								</td>
								<td>
									<input name="value[{id}]" class="form-control" type="text" value="{goods_name}"/>
								</td>
							</tr>
						</xsl:for-each>
					</table>
					<input class="btn btn-success" type="submit" value="Сохранить названия"/>
				</form>
			</div>
		</div>

		<hr/>

	</xsl:template>
</xsl:stylesheet>
