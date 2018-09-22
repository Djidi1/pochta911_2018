<?php
	header('Content-Type: text/html; charset=UTF-8');
$d = $_GET["d"];
?>
<div id="callme">
	<input type="button" id="viewform" class="callme_viewform">
</div>
<div class="cme_form">
	<a class="cme_cls" href="/callme/">&times;</a>
<?php

$title = ($d["title"] ? $d["title"] : "Заказать бесплатный звонок");
$button = ($d["button"] ? $d["button"] : "Перезвоните мне");
$f = (strlen($d["fields"]) === 0 ? "Имя,Телефон,-Комментарий" : $d["fields"]);

echo "<h6>".$title."</h6>";

$f = str_replace(", ", ",", $f);
$f = str_replace("'", "\"", $f);
$f = explode(",", $f);
for ($i = 0; $i < count($f); $i++){
	if ($f[$i][0] == "-") {
		echo "<span>".substr($f[$i], 1)."</span>";
		echo "<span><textarea placeholder='".substr($f[$i], 1)."' class='cme_txt'></textarea></span>";
	} elseif ($f[$i][0] == "!") {
		$str = substr($f[$i], 1);
		$str = explode("!", $str);
		echo "<span>".$str[0]."</span>";
		echo"<select class='cme_select' name='".$str[0]."'>";
		for ($j = 1; $j < count($str); $j++) {
			echo "<option value=".$str[$j].">".$str[$j]."</option>";
		}
		echo"</select>";
	} else {
		echo "<span>".$f[$i]."</span>";
		echo"<span><input placeholder='".$f[$i]."' class='cme_txt' type='text' maxlength='150'></span>";
	}
}

function echoTime($x) // формат часов
{
	$rs = ($x > 24 ? $x-24 : $x);
	$rs = (strlen($rs) == 1 ? "0".$rs : $rs);// до 10 утра, добавим ноль
	return $rs;
}

// hr - текущее время
// time_end конец работы
// time_start начало работы

if ($d["calltime"] == 1) {
	$hr = ($d["hr"] ? $d["hr"] : date("H"));

	$time_start = (strlen($d['time_start']) > 0 ? $d['time_start'] : 8); // если не указано начало дня
	$time_end   = (strlen($d['time_end']) > 0 ? $d['time_end'] : 24); // если не указан конец дня
	$hr_msg = ($hr > $time_end ? "завтра" : "сегодня"); // закончился ли день
	$hr = ($hr > $time_end ? $time_start : $hr); 
	$hr = ($hr < $time_start ? $time_start : $hr);

	echo "
	<span class='cme_ct'>
	<div>Время звонка</div>
	<div>".$hr_msg."</div>
	<div><select class='cme_ct_start'>
	<option>~</option>";

	for ($i = $hr; $i <= $time_end; $i++)
	{
		echo '<option value="'.$i.'">'.echoTime($i).'</option>';
	}

	echo"</select></div>
	<div>до</div><div>
	<select class='cme_ct_finish'><option>~</option>";

	for ( $i = $hr + 1; $i <= $time_end + 1; $i++ )
	{
		echo '<option disabled value="'.$i.'">'.echoTime($i).'</option>';
	}

	echo "</select></div>
	<div>час.</div>
	</span>";
}
?>
	<span class="cme_btn_place">
		<button class="cme_btn"><?php echo $button; ?></button>
	</span>
	<div class="callme_result"></div>
</div>
<div id="cme_back"></div>