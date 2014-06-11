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
 * Methods supporting a list of event records.
 *
 * @package     EventSlideshow.Administrator
 * @subpackage  com_eventslideshow
 * @since       1.6
 */
class EventslideshowModelEvents extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'catid', 'a.catid', 'category_title', 'category_id',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'datetype', 'a.datetype',
				'startmonth', 'a.startmonth',
				'startday', 'a.startday',
				'endmonth', 'a.endmonth',
				'endday', 'a.endday',
				'showtitle', 'a.showtitle'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$dateType = $this->getUserStateFromRequest($this->context . '.filter.datetype', 'filter_datetype', '', 'string');
		$this->setState('filter.datetype', $dateType);

		$startMonth = $this->getUserStateFromRequest($this->context . '.filter.startmonth', 'filter_startmonth', null, 'int');
		$this->setState('filter.startmonth', $startMonth);

		$startDay = $this->getUserStateFromRequest($this->context . '.filter.startday', 'filter_startday', null, 'int');
		$this->setState('filter.startday', $startDay);

		$endMonth = $this->getUserStateFromRequest($this->context . '.filter.endmonth', 'filter_endmonth', null, 'int');
		$this->setState('filter.endmonth', $endMonth);

		$endDay = $this->getUserStateFromRequest($this->context . '.filter.endday', 'filter_endday', null, 'int');
		$this->setState('filter.endday', $endDay);

		$published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_eventslideshow');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id    A prefix for the store id.
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.language');
		$id .= ':' . $this->getState('filter.datetype');
		$id .= ':' . $this->getState('filter.startmonth');
		$id .= ':' . $this->getState('filter.startday');
		$id .= ':' . $this->getState('filter.endmonth');
		$id .= ':' . $this->getState('filter.endday');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid,' .
					'a.datetype, a.startmonth, a.startday, a.endmonth, a.endday, a.image,' .
					'a.state, a.ordering,' .
					'a.language'
			)
		);
		$query->from($db->quoteName('#__eventslideshow_events') . ' AS a');

		// Join over the language
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

		// Join over the categories.
		$query->select('c.title AS category_title')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Filter by date type
		$datetype = $this->getState('filter.datetype');
		if ($datetype)
		{
			$query->where("a.datetype = " . $db->quote($db->escape($datetype)));
		}

		// Filter by start month
		$startmonth = $this->getState('filter.startmonth');
		if (is_numeric($startmonth))
		{
			$query->where('a.startmonth >= ' . (int) $startmonth);
		}

		// Filter by start day
		$startday = $this->getState('filter.startday');
		if (is_numeric($startday))
		{
			$query->where('a.startday >= ' . (int) $startday);
		}

		// Filter by end month
		$endmonth = $this->getState('filter.endmonth');
		if (is_numeric($endmonth))
		{
			$query->where('a.endmonth <= ' . (int) $endmonth);
		}

		// Filter by end day
		$endday = $this->getState('filter.endday');
		if (is_numeric($endday))
		{
			$query->where('a.endday <= ' . (int) $endday);
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId))
		{
			$query->where('a.catid = ' . (int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'ordering');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		if ($orderCol == 'ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'c.title ' . $orderDirn . ', a.ordering';
		}
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
