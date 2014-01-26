<?php
/**
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventslideshow
 *
 * @copyright   Copyright (C) 2012 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of events.
 *
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventslideshow
 * @since       1.5
 */
class EventslideshowViewEvents extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		EventslideshowHelper::addSubmenu('events');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/eventslideshow.php';

		$state = $this->get('State');
		$canDo = JHelperContent::getActions($state->get('filter.category_id'), 0, 'com_eventslideshow');
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_EVENTSLIDESHOW_MANAGER_EVENTS'), 'calendar events');

		if (count($user->getAuthorisedCategories('com_eventslideshow', 'core.create')) > 0)
		{
			JToolbarHelper::addNew('event.add');
		}

		if (($canDo->get('core.edit')))
		{
			JToolbarHelper::editList('event.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				JToolbarHelper::publish('events.publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('events.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1)
			{
				if ($this->state->get('filter.state') != 2)
				{
					JToolbarHelper::archiveList('events.archive');
				}
				elseif ($this->state->get('filter.state') == 2)
				{
					JToolbarHelper::unarchiveList('events.publish');
				}
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::checkin('events.checkin');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'events.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('events.trash');
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_eventslidesow') && $user->authorise('core.edit', 'com_eventslideshow') && $user->authorise('core.edit.state', 'com_eventslideshow'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($user->authorise('core.admin', 'com_eventslideshow'))
		{
			JToolbarHelper::preferences('com_eventslideshow');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering'   => JText::_('JGRID_HEADING_ORDERING'),
			'a.state'      => JText::_('JSTATUS'),
			'a.title'      => JText::_('JGLOBAL_TITLE'),
			'a.datetype'   => JText::_('COM_EVENTSLIDESHOW_HEADING_DATETYPE'),
			'a.startmonth' => JText::_('COM_EVENTSLIDESHOW_HEADING_STARTMONTH'),
			'a.startday'   => JText::_('COM_EVENTSLIDESHOW_HEADING_STARTDAY'),
			'a.endmonth'   => JText::_('COM_EVENTSLIDESHOW_HEADING_ENDMONTH'),
			'a.endday'     => JText::_('COM_EVENTSLIDESHOW_HEADING_ENDDAY'),
			'a.language'   => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id'         => JText::_('JGRID_HEADING_ID')
		);
	}
}
