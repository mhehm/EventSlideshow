<?php
/**
 * @package     EventSlideshow.Site
 * @subpackage  mod_eventslideshow
 *
 * @copyright   Copyright (C) 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once JPATH_ADMINISTRATOR . '/components/com_eventslideshow/helpers/eventslideshow.php';

$list = EventslideshowHelper::getEventItems($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_eventslideshow', $params->get('layout', 'default'));
