<?php
/**
 * @package     EventSlideshow.Site
 * @subpackage  mod_eventslideshow
 *
 * @copyright   Copyright (C) 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.framework');

if($count = count($list)) :

$script   = "jQuery(function(){jQuery('.carousel').carousel()});";
$document = JFactory::getDocument();
$document->addScriptDeclaration($script);
?>
<div id="esCarousel_<?php echo $module->id; ?>" class="carousel slide<?php echo $moduleclass_sfx; ?>" data-interval="<?php echo $params->get('interval', '5000'); ?>" data-pause="<?php echo $params->get('pause', 'hover'); ?>" data-wrap="<?php echo $params->get('wrap', 'true'); ?>" data-ride="carousel">
	<?php if($params->get('show_indicators', 1) && ($count != 1)) : ?>
	<ol class="carousel-indicators">
		<?php foreach($list as $i => $item) : ?>
		<li data-target="#esCarousel_<?php echo $module->id; ?>" data-slide-to="<?php echo $i;?>" class="<?php if($i == 0) echo 'active'; ?>"></li>
		<?php endforeach; ?>
	</ol>
	<?php endif; ?>
	<div class="carousel-inner">
		<?php foreach($list as $i => $item) : ?>
		<div class="item<?php if ($i == 0) echo ' active'; ?>">
			<img src="<?php echo JURI::root(true) . '/' . $item->image; ?>" alt="<?php echo $item->title; ?>" />
			<?php if($params->get('title') || ($params->get('description') && $item->description)) : ?>
			<div class="carousel-caption">
				<?php if($params->get('title') && $item->showtitle) : ?>
				<h4>
					<?php if($item->link) echo '<a href="' . $item->link . '">'; ?>
					<?php echo $item->title; ?>
					<?php if($item->link) echo '</a>'; ?>
				</h4>
				<?php endif; ?>
				<?php if($params->get('description') && $item->description) : ?>
				<p><?php echo $item->description; ?></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
	<?php if($params->get('show_nav', 1) && ($count != 1)) : ?>
	<a class="carousel-control left" href="#esCarousel_<?php echo $module->id; ?>" data-slide="prev"><span class="glyphicon glyphicon-chevron-left">‹</span></a>
	<a class="carousel-control right" href="#esCarousel_<?php echo $module->id; ?>" data-slide="next"><span class="glyphicon glyphicon-chevron-right">›</span></a>
	<?php endif; ?>
</div>
<?php endif; ?>