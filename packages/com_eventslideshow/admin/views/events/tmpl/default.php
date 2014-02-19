<?php
/**
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventslideshow
 *
 * @copyright   Copyright (C) 2012 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder  = $user->authorise('core.edit.state', 'com_eventslideshow.category');
$archived  = $this->state->get('filter.state') == 2 ? true : false;
$trashed   = $this->state->get('filter.state') == -2 ? true : false;
$params    = (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_eventslideshow&task=events.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'eventList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_eventslideshow&view=events'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="eventList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="hidden-phone">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JText::_('COM_EVENTSLIDESHOW_HEADING_IMAGE'); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th class="center nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_EVENTSLIDESHOW_HEADING_DATETYPE', 'a.datetype', $listDirn, $listOrder); ?>
						</th>
						<th class="center nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_EVENTSLIDESHOW_HEADING_STARTMONTH', 'a.startmonth', $listDirn, $listOrder); ?>
						</th>
						<th class="center nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_EVENTSLIDESHOW_HEADING_STARTDAY', 'a.startday', $listDirn, $listOrder); ?>
						</th>
						<th class="center nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_EVENTSLIDESHOW_HEADING_ENDMONTH', 'a.endmonth', $listDirn, $listOrder); ?>
						</th>
						<th class="center nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_EVENTSLIDESHOW_HEADING_ENDDAY', 'a.endday', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="12">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering  = ($listOrder == 'ordering');
					$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_eventslideshow&task=edit&type=other&cid[]=' . $item->catid);
					$canCreate  = $user->authorise('core.create',     'com_eventslideshow.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_eventslideshow.category.' . $item->catid);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_eventslideshow.category.' . $item->catid) && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler <?php echo $iconClass ?>">
								<i class="icon-menu"></i>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5"
									value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'events.', $canChange, 'cb'); ?>
								<?php
								// Create dropdown items
								$action = $archived ? 'unarchive' : 'archive';
								JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'events');

								$action = $trashed ? 'untrash' : 'trash';
								JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'events');

								// Render dropdown list
								echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
								?>
							</div>
						</td>
						<td class="center nowrap">
							<a title="<?php echo JHtml::tooltipText('COM_EVENTSLIDESHOW_CLICK_TO_PREVIEW'); ?>" class="btn btn-micro modal hasTooltip" href="<?php echo JUri::root('true') . '/' . $item->image; ?>"><i class="icon-picture "></i></a>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_eventslideshow&task=event.edit&id='.(int) $item->id); ?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>
								<span class="small">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
								</span>
								<div class="small">
									<?php echo $this->escape($item->category_title); ?>
								</div>
							</div>
						</td>
						<td class="center nowrap">
							<?php echo JText::_('COM_EVENTSLIDESHOW_DATE_' . $item->datetype); ?>
						</td>
						<td class="center nowrap">
							<?php echo (int) $item->startmonth; ?>
						</td>
						<td class="center nowrap">
							<?php echo (int) $item->startday; ?>
						</td>
						<td class="center nowrap">
							<?php echo (int) $item->endmonth;?>
						</td>
						<td class="center nowrap">
							<?php echo (int) $item->endday; ?>
						</td>
						<td class="small center nowrap hidden-phone">
							<?php if ($item->language == '*'):?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php endif;?>
						</td>
						<td class="center hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php //Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
