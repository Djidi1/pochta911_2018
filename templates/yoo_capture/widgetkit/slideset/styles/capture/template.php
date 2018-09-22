<?php
/**
* @package   yoo_capture
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$sets      = array();
	$nav       = array();

	if (is_numeric($settings['items_per_set'])) {
		
		$sets = array_chunk($widget->items, $settings['items_per_set']);

	} else {
	
		foreach ($widget->items as $key => $item) {
			
			if (!isset($sets[$item['set']])) {
				$sets[$item['set']] = array();
			}

			$sets[$item['set']][] = $item;
		}

	}

	foreach (array_keys($sets) as $s) {
		$nav[] = ($settings['navigation'] == 2) ? '<li><span>'.$s.'</span></li>' : '<li><span></span></li>';
	}

	$i = 0;
?>
<div id="slideset-<?php echo $widget_id;?>" class="wk-slideset wk-slideset-capture" data-widgetkit="slideset" data-options='<?php echo json_encode($settings); ?>'>

	<div>
		<div class="sets">
			<?php foreach ($sets as $set => $items) : ?>
				<ul class="set">
					<?php foreach ($items as $item) : ?>
					<?php 
						/* Lazy Loading */
						$item["content"] = ($i==$settings['index']) ? $item["content"] : $this['image']->prepareLazyload($item["content"]);
					?>
					<li>
						<div class="wk-container">
							<h4 class="title uk-visible-large"><?php echo $item['title']; ?></h4>
							<div class="uk-visible-large"><?php echo $item['img_description']; ?></div>
							<article class="wk-content">
								<?php echo $item['content']; ?>
								<?php if($settings['title']): ?>
								<?php endif; ?>
							</article>
						</div>
							
					</li>
					<?php endforeach; ?>
				</ul>
				<?php $i=$i+1;?>
			<?php endforeach; ?>
		</div>
		<?php if ($settings['buttons']): ?><div class="next"><a class="uk-icon-button uk-icon-chevron-right uk-float-left"></a></div><div class="prev"><a class="uk-icon-button uk-icon-chevron-left uk-float-right"></a></div><?php endif; ?>
	</div>
	<?php if ($settings['navigation'] && count($nav)) : ?>
	<ul class="nav <?php echo ($settings['navigation'] == 1) ? 'icon' : 'text'; ?>"><?php echo implode('', $nav); ?></ul>
	<?php endif; ?>

</div>