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

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'event.cancel' || document.formvalidator.isValid(document.id('event-form')))
		{
			Joomla.submitform(task, document.getElementById('event-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_eventslideshow&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="event-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('COM_EVENTSLIDESHOW_NEW_EVENT', true) : JText::sprintf('COM_EVENTSLIDESHOW_EDIT_EVENT', $this->item->id, true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<?php echo $this->form->getControlGroup('image'); ?>
				<?php echo $this->form->getControlGroup('clickarticle'); ?>
				<?php echo $this->form->getControlGroup('clickurl'); ?>
				<?php echo $this->form->getControlGroup('description'); ?>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<?php echo $this->form->getControlGroup('datetype'); ?>
			<div class="control-group">
				<div class="control-label hasTooltip" title="<?php echo JHtml::tooltipText('JGLOBAL_FIELD_PUBLISH_UP_DESC'); ?>">
					<?php echo JText::_('JGLOBAL_FIELD_PUBLISH_UP_LABEL'); ?> *
				</div>
				<div class="controls form-inline">
					<?php echo $this->form->getLabel('startmonth'); ?>
					<?php echo $this->form->getInput('startmonth'); ?>
					<?php echo $this->form->getLabel('startday'); ?>
					<?php echo $this->form->getInput('startday'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label hasTooltip" title="<?php echo JHtml::tooltipText('JGLOBAL_FIELD_PUBLISH_DOWN_DESC'); ?>">
					<?php echo JText::_('JGLOBAL_FIELD_PUBLISH_DOWN_LABEL'); ?> *
				</div>
				<div class="controls form-inline">
					<?php echo $this->form->getLabel('endmonth'); ?>
					<?php echo $this->form->getInput('endmonth'); ?>
					<?php echo $this->form->getLabel('endday'); ?>
					<?php echo $this->form->getInput('endday'); ?>
				</div>
			</div>
			<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
