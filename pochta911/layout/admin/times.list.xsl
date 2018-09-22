<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'timeslist']">
		<h2>Ограничения по времени</h2>
		<form method="post" class="time_check_list">
			<input type="hidden" name="sub_action" value="save"/>
			<ul class="list-group list-group-hover list-group-striped">
				<li class="list-group-item">Заказ на завтра с доставкой до <input name="period_tomarrow[from]" class="form-control" type="number" step="0.1" value="{times/period_tomarrow/from}"/> ч. можно оставлять до <input name="period_tomarrow[to]" class="form-control" type="number" value="{times/period_tomarrow/to}"/> ч.</li>

				<li class="list-group-item">Заказ на сегодня с доставкой до <input name="period_today[from]" class="form-control" type="number" step="0.1" value="{times/period_today/from}"/> ч. можно оставлять с <input name="period_today[to]" class="form-control" type="number" step="0.1" value="{times/period_today/to}"/> ч.</li>

				<li class="list-group-item">Заказ с готовностью с <input name="ready_1[from]" class="form-control" type="number" step="0.1" value="{times/ready_1/from}"/> до <input name="ready_1[to]" class="form-control" type="number" step="0.1" value="{times/ready_1/to}"/> можно доставить в течении <input name="ready_1[period]" class="form-control" type="number" step="0.1" value="{times/ready_1/period}"/> ч.</li>

				<li class="list-group-item">Стандартный период доставки <input name="ready_2[period]" class="form-control" type="number" step="0.1" value="{times/ready_2/period}"/> ч. от времени готовности.</li>

				<li class="list-group-item">Стандартный период начала доставки <input name="ready_3[period]" class="form-control" type="number" step="0.1" value="{times/ready_3/period}"/> ч. от времени готовности "по".</li>

				<li class="list-group-item">Крайнее время доставки не может быть меньше <input name="ready_today[period]" class="form-control" type="number" step="0.1" value="{times/ready_today/period}"/>  ч. от времени заказа.</li>

				<li class="list-group-item">Период доставки всех заказов не менее <input name="period[period]" class="form-control" type="number" step="1" value="{times/period/period}"/> минут. (Кроме заказов к точному времени)</li>

				<li class="list-group-item">Интервал забора не менее <input name="period_from[period]" class="form-control" type="number" step="1" value="{times/period_from/period}"/> минут.</li>

			</ul>
			<input class="btn btn-success" type="submit" value="Сохранить"/>
		</form>
	</xsl:template>
</xsl:stylesheet>
