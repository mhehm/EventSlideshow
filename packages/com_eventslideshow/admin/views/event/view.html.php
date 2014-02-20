<?php
/**
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventlideshow
 *
 * @copyright   Copyright (C) 2012 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('EventslideshowHelper', JPATH_COMPONENT . '/helpers/eventslideshow.php');

/**
 * View to edit a event.
 *
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventlideshow
 * @since       1.5
 */
class EventslideshowViewEvent extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user       = JFactory::getUser();
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$authCats   = count($user->getAuthorisedCategories('com_eventslideshow', 'core.create'));

		// Since we don't track these assets at the item level, use the category id.
		$canDo      = JHelperContent::getActions('com_eventslideshow', 'category', $this->item->catid);

		JToolbarHelper::title(JText::_('COM_EVENTSLIDESHOW_MANAGER_EVENT'), 'calendar eventslideshow');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || $authCats))
		{
			JToolbarHelper::apply('event.apply');
			JToolbarHelper::save('event.save');
		}

		if (!$checkedOut && $authCats)
		{
			JToolbarHelper::save2new('event.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $authCats)
		{
			JToolbarHelper::save2copy('event.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('event.cancel');
		}
		else
		{
			JToolbarHelper::cancel('event.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::help('screen.monthes', true);
	}
}
