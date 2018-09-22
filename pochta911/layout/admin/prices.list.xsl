<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'priceslist']">
		<h2>Стоимость за киллометр</h2>
		<form method="post">
			<input type="hidden" name="sub_action" value="save"/>
			<div>
				<table class="table table-hover">
				<tbody>
					<tr>
						<th>Стоимость пересечения Невы</th>
						<td><input name="km_neva" class="form-control" type="number" value="{prices/add_item/km_neva}"/></td>
					</tr>
					<tr>
						<th>За пределами Санкт-Петербурга</th>
						<td><input name="km_kad" class="form-control" type="number" value="{prices/add_item/km_kad}"/></td>
					</tr>
					<tr>
						<th>За попадание в Геозону за КАДом</th>
						<td><input name="km_geozone" class="form-control" type="number" value="{prices/add_item/km_geozone}"/></td>
					</tr>
					<tr>
						<th>Стоимость во Всеволожск (вместо за КАДом)</th>
						<td><input name="km_vsevol" class="form-control" type="number" value="{prices/add_item/km_vsevol}"/></td>
					</tr>
					<tr>
						<th>Прибавление к стоимости за "к точному времени"</th>
						<td><input name="km_target" class="form-control" type="number" value="{prices/add_item/km_target}"/></td>
					</tr>
				</tbody>
				</table>
				<table class="table table-hover prices">
					<thead>
						<tr>
							<th>id</th>
							<th>от</th>
							<th>до</th>
							<th>стоимость</th>
							<th>
							</th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="prices/item">
							<tr>
								<td>
									<xsl:value-of select="id"/>
								</td>
								<td>
									<input name="km_from[]" class="form-control" type="number" value="{km_from}"/>
								</td>
								<td>
									<input name="km_to[]" class="form-control" type="number" value="{km_to}"/>
								</td>
								<td>
									<input name="km_cost[]" class="form-control" type="number" value="{km_cost}"/>
								</td>
								<td>
									<span class="btn btn-danger btn-xs" title="Удалить строку" onclick="$(this).parent().parent().remove()">
										<i class="fa fa-trash" aria-hidden="true"/>
									</span>
								</td>
							</tr>
						</xsl:for-each>
						<tr>
							<td>
							</td>
							<td>
								<input name="km_from[]" class="form-control" type="number" value=""/>
							</td>
							<td>
								<input name="km_to[]" class="form-control" type="number" value=""/>
							</td>
							<td>
								<input name="km_cost[]" class="form-control" type="number" value=""/>
							</td>
							<td>
							</td>
						</tr>
					</tbody>
				</table>
				<span class="btn btn-info btn-sm" onclick="$('table.prices').find('tbody tr:last').after($('table.prices').find('tbody tr:last').clone());">Добавить цену</span>
			</div>
			<div align="right">
				<input class="btn btn-success" type="submit" value="Сохранить"/>
			</div>
		</form>
	</xsl:template>
</xsl:stylesheet>
