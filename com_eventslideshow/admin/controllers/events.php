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
 * Events list controller class.
 *
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventslideshow
 * @since       1.6
 */
class EventslideshowControllerEvents extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since   1.6
	 */
	public function getModel($name = 'Event', $prefix = 'EventslideshowModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
