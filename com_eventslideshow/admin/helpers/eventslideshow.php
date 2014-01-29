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
 * Event Slideshow helper.
 *
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventslideshow
 * @since       1.6
 */
class EventslideshowHelper extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	$vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName = 'events')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTSLIDESHOW_SUBMENU_EVENTS'),
			'index.php?option=com_eventslideshow&view=events',
			$vName == 'events'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTSLIDESHOW_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_eventslideshow',
			$vName == 'categories'
		);
	}
}
