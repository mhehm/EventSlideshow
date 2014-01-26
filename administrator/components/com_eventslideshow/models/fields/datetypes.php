<?php
/**
 * @package     EventSlideshow.Libraries
 * @subpackage  com_eventslideshow
 *
 * @copyright   Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('predefinedlist');

/**
 * Form Field to load a list of date types
 *
 * @package     EventSlideshow.Libraries
 * @subpackage  com_eventslideshow
 * @since       3.2
 */
class JFormFieldDateTypes extends JFormFieldPredefinedList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $type = 'DateTypes';

	/**
	 * Available date types
	 *
	 * @var  array
	 * @since  3.2
	 */
	protected $predefinedOptions = array(
		'AG' => 'COM_EVENTSLIDESHOW_DATE_AG',
		'AP' => 'COM_EVENTSLIDESHOW_DATE_AP',
		'AD' => 'COM_EVENTSLIDESHOW_DATE_AD'
	);
}
