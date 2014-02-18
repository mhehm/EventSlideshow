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

	public static function getEventItems($params)
	{
		$com_params = JComponentHelper::getParams('com_eventslideshow');

		// Gregorian Date
		$gTodayDate  = new JDate('now');
		$gTodayMonth = $gTodayDate->calendar('n');
		$gTodayDay   = $gTodayDate->calendar('j');

		// Solar Date
		$sTodayDate  = new SDate('now');
		$sTodayMonth = $sTodayDate->calendar('n');
		$sTodayDay   = $sTodayDate->calendar('j');

		// Lunar Date
		$adjustment  = $com_params->get('lunar_hijri_adjustment', 0);
		$lTodayDate  = new LDate('now');
		$lTodayMonth = $lTodayDate->calendar('n', false, true, $adjustment);
		$lTodayDay   = $lTodayDate->calendar('j', false, true, $adjustment);

		// get a reference to the database
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select($db->quoteName(array('id', 'title', 'image', 'clickurl', 'clickarticle', 'description', 'showtitle')))
			->from($db->quoteName('#__eventslideshow_events'))
			->where($db->quoteName('state') . ' = ' . $db->quote('1'))
			->order('ordering')
			->where("((datetype = 'AG' AND startmonth <= " . $gTodayMonth . " AND startday <= " . $gTodayDay . " AND endmonth >= " . $gTodayMonth . " AND endday >= " . $gTodayDay . ") OR
					  (datetype = 'AP' AND startmonth <= " . $sTodayMonth . " AND startday <= " . $sTodayDay . " AND endmonth >= " . $sTodayMonth . " AND endday >= " . $sTodayDay . ") OR
					  (datetype = 'AD' AND startmonth <= " . $lTodayMonth . " AND startday <= " . $lTodayDay . " AND endmonth >= " . $lTodayMonth . " AND endday >= " . $lTodayDay . "))");

		// Filter by language
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		if ($model->getState('filter.language'))
		{
			$query->where('language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		if($categories = $params->get('catid'))
		{
			$query->where('catid IN (' . implode(',', $categories) . ')');
		}

		$db->setQuery($query);

		$items = $db->loadObjectList();
		
		foreach($items as $item)
		{
			$item->link = '';

			if($item->clickarticle)
			{
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->clickarticle));
			}
			elseif($item->clickurl)
			{
				$item->link = JRoute::_($item->clickurl);
			}
		}

		return $items;
	}
}

// Solar Date Class
class SDate extends JDate
{
	const DAY_NUMBER    = "\x027\x03";
	const DAY_NUMBER2   = "\x030\x03";
	const DAY_YEAR      = "\x032\x03";
	const MONTH_ABBR    = "\x033\x03";
	const MONTH_NAME    = "\x034\x03";
	const MONTH_NUMBER  = "\x035\x03";
	const MONTH_NUMBER2 = "\x036\x03";
	const MONTH_LENGTH  = "\x037\x03";
	const YEAR_ABBR     = "\x040\x03";
	const YEAR_NAME     = "\x041\x03";
	const AM_LOWER      = "\x042\x03";
	const AM_UPPER      = "\x043\x03";
	const SOLAR_EPOCH   = 1948320.5;

	/**
	 * Translates month number to a string.
	 *
	 * @param   integer  $month  The numeric month of the year.
	 * @param   boolean  $abbr   If true, return the abbreviated month string
	 *
	 * @return  string  The month of the year.
	 *
	 * @since   11.1
	 */
	public function monthToString($month, $abbr = false)
	{
		switch ($month)
		{
			case 1:
				return $abbr ? JText::_('FARVARDIN_SHORT') : JText::_('FARVARDIN');
			case 2:
				return $abbr ? JText::_('ORDIBEHESHT_SHORT') : JText::_('ORDIBEHESHT');
			case 3:
				return $abbr ? JText::_('KHORDAD_SHORT') : JText::_('KHORDAD');
			case 4:
				return $abbr ? JText::_('TIR_SHORT') : JText::_('TIR');
			case 5:
				return $abbr ? JText::_('MORDAD_SHORT') : JText::_('MORDAD');
			case 6:
				return $abbr ? JText::_('SHAHRIVAR_SHORT') : JText::_('SHAHRIVAR');
			case 7:
				return $abbr ? JText::_('MEHR_SHORT') : JText::_('MEHR');
			case 8:
				return $abbr ? JText::_('ABAN_SHORT') : JText::_('ABAN');
			case 9:
				return $abbr ? JText::_('AZAR_SHORT') : JText::_('AZAR');
			case 10:
				return $abbr ? JText::_('DEY_SHORT') : JText::_('DEY');
			case 11:
				return $abbr ? JText::_('BAHMAN_SHORT') : JText::_('BAHMAN');
			case 12:
				return $abbr ? JText::_('ESFAND_SHORT') : JText::_('ESFAND');
		}
	}

	/**
	 * Gets the date as a formatted string in a local calendar.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 *
	 * @return  string   The date string in the specified format format.
	 *
	 * @since   11.1
	 */
	public function calendar($format, $local = false, $translate = true)
	{
		// Do string replacements for date format options that can be translated.
		$format = preg_replace('/(^|[^\\\])d/', "\\1".self::DAY_NUMBER2, $format);
		$format = preg_replace('/(^|[^\\\])j/', "\\1".self::DAY_NUMBER, $format);
		$format = preg_replace('/(^|[^\\\])z/', "\\1".self::DAY_YEAR, $format);
		$format = preg_replace('/(^|[^\\\])M/', "\\1".self::MONTH_ABBR, $format);
		$format = preg_replace('/(^|[^\\\])F/', "\\1".self::MONTH_NAME, $format);
		$format = preg_replace('/(^|[^\\\])n/', "\\1".self::MONTH_NUMBER, $format);
		$format = preg_replace('/(^|[^\\\])m/', "\\1".self::MONTH_NUMBER2, $format);
		$format = preg_replace('/(^|[^\\\])t/', "\\1".self::MONTH_LENGTH, $format);
		$format = preg_replace('/(^|[^\\\])y/', "\\1".self::YEAR_ABBR, $format);
		$format = preg_replace('/(^|[^\\\])Y/', "\\1".self::YEAR_NAME, $format);
		$format = preg_replace('/(^|[^\\\])a/', "\\1".self::AM_LOWER, $format);
		$format = preg_replace('/(^|[^\\\])A/', "\\1".self::AM_UPPER, $format);

		// Format the date.
		$return = parent::calendar($format, $local);

		$jd        = gregoriantojd($this->month, $this->day, $this->year);
		$solarDate = self::jdtosolar($jd);
		$m         = $solarDate['mon'];
		$d         = $solarDate['day'];
		$y         = $solarDate['year'];

		// Manually modify the strings in the formated time.
		if (strpos($return, self::DAY_NUMBER) !== false)
		{
			$return = str_replace(self::DAY_NUMBER, $d , $return);
		}
		if (strpos($return, self::DAY_NUMBER2) !== false)
		{
			$return = str_replace(self::DAY_NUMBER2, sprintf("%02d",$d), $return);
		}
		if (strpos($return, self::DAY_YEAR) !== false)
		{
			$return = str_replace(self::DAY_YEAR, $jd - self::solartojd(1,1,$y)+1, $return);
		}
		if (strpos($return, self::MONTH_ABBR) !== false)
		{
			$return = str_replace(self::MONTH_ABBR, $this->monthToString($m, true), $return);
		}
		if (strpos($return, self::MONTH_NAME) !== false)
		{
			$return = str_replace(self::MONTH_NAME, $this->monthToString($m), $return);
		}
		if (strpos($return, self::MONTH_NUMBER) !== false)
		{
			$return = str_replace(self::MONTH_NUMBER, $m , $return);
		}
		if (strpos($return, self::MONTH_NUMBER2) !== false) {
			$return = str_replace(self::MONTH_NUMBER2, sprintf("%02d", $m) , $return);
		}
		if (strpos($return, self::MONTH_LENGTH) !== false)
		{
			$return = str_replace(self::MONTH_LENGTH, $m < 7 ? 31 : $m < 12 ? 30 : self::leap_solar($y) ? 30 : 29 , $return);
		}
		if (strpos($return, self::YEAR_ABBR) !== false)
		{
			$return = str_replace(self::YEAR_ABBR, sprintf("%02d",$y % 100), $return);
		}
		if (strpos($return, self::YEAR_NAME) !== false)
		{
			$return = str_replace(self::YEAR_NAME, $y, $return);
		}
		if (strpos($return, self::AM_LOWER) !== false)
		{
			$return = str_replace(self::AM_LOWER, $this->format('a',$local)=='pm' ? JText::_('PM_SHORT') : JText::_('AM_SHORT'), $return);
		}
		if (strpos($return, self::AM_UPPER) !== false)
		{
			$return = str_replace(self::AM_UPPER, $this->format('a',$local)=='pm' ? JText::_('PM') : JText::_('AM'), $return);
		}

		return $return;
	}

	public static function jdtosolar($jd)
	{
		$jd     = floor($jd) + 0.5;
		$depoch = $jd - self::solartojd(1, 1, 475);
		$cycle  = floor($depoch / 1029983);
		$cyear  = $depoch % 1029983;

		if ($cyear == 1029982)
		{
			$ycycle = 2820;
		}
		else
		{
			$aux1   = floor($cyear / 366);
			$aux2   = $cyear % 366;
			$ycycle = floor(((2134 * $aux1) + (2816 * $aux2) + 2815) / 1028522) + $aux1 + 1;
		}

		$year = $ycycle + (2820 * $cycle) + 474;

		if ($year <= 0)
		{
			$year--;
		}

		$yday  = ($jd - self::solartojd(1, 1, $year)) + 1;
		$month = ($yday <= 186) ? ceil($yday / 31) : ceil(($yday - 6) / 30);
		$day   = ($jd - self::solartojd($month, 1, $year)) + 1;

		return array('year'=>$year, 'mon'=>$month,'day'=> $day);
	}

	public static function solartojd($month, $day, $year)
	{
		$epbase = $year - (($year >= 0) ? 474 : 473);
		$epyear = 474 + $epbase % 2820;

		return $day +
				(($month <= 7) ?
					(($month - 1) * 31) :
					((($month - 1) * 30) + 6)
				) +
				floor((($epyear * 682) - 110) / 2816) +
				($epyear - 1) * 365 +
				floor($epbase / 2820) * 1029983 +
				(self::SOLAR_EPOCH);
	}

	public static function leap_solar($year)
	{
		return (((((($year - (($year > 0) ? 474 : 473)) % 2820) + 474) + 38) * 682) % 2816) < 682;
	}
}

// Lunar Date Class
class LDate extends JDate
{
	const DAY_NUMBER    = "\x027\x03";
	const DAY_NUMBER2   = "\x030\x03";
	const DAY_YEAR      = "\x032\x03";
	const MONTH_ABBR    = "\x033\x03";
	const MONTH_NAME    = "\x034\x03";
	const MONTH_NUMBER  = "\x035\x03";
	const MONTH_NUMBER2 = "\x036\x03";
	const MONTH_LENGTH  = "\x037\x03";
	const YEAR_ABBR     = "\x040\x03";
	const YEAR_NAME     = "\x041\x03";
	const AM_LOWER      = "\x042\x03";
	const AM_UPPER      = "\x043\x03";
	const LUNAR_EPOCH   = 1948439.5;

	public static $lunar_day_setting = 0;

	/**
	 * Translates month number to a string.
	 *
	 * @param   integer  $month  The numeric month of the year.
	 * @param   boolean  $abbr   If true, return the abbreviated month string
	 *
	 * @return  string  The month of the year.
	 *
	 * @since   11.1
	 */
	public function monthToString($month, $abbr = false)
	{
		switch ($month)
		{
			case 1:
				return $abbr ? JText::_('MUHARRAM_SHORT') : JText::_('MUHARRAM');
			case 2:
				return $abbr ? JText::_('SAFAR_SHORT') : JText::_('SAFAR');
			case 3:
				return $abbr ? JText::_('RABI_AL_AWWAL_SHORT') : JText::_('RABI_AL_AWWAL');
			case 4:
				return $abbr ? JText::_('RABI_AL_THANI_SHORT') : JText::_('RABI_AL_THANI');
			case 5:
				return $abbr ? JText::_('JAMADA_AL_AWWAL_SHORT') : JText::_('JAMADA_AL_AWWAL');
			case 6:
				return $abbr ? JText::_('JAMADA_AL_THANI_SHORT') : JText::_('JAMADA_AL_THANI');
			case 7:
				return $abbr ? JText::_('RAJAB_SHORT') : JText::_('RAJAB');
			case 8:
				return $abbr ? JText::_('SHAABAN_SHORT') : JText::_('SHAABAN');
			case 9:
				return $abbr ? JText::_('RAMADAN_SHORT') : JText::_('RAMADAN');
			case 10:
				return $abbr ? JText::_('SHAWWAL_SHORT') : JText::_('SHAWWAL');
			case 11:
				return $abbr ? JText::_('DHU_AL_QIDAH_SHORT') : JText::_('DHU_AL_QIDAH');
			case 12:
				return $abbr ? JText::_('DHU_AL_HIJJAH_SHORT') : JText::_('DHU_AL_HIJJAH');
		}
	}

	/**
	 * Gets the date as a formatted string in a local calendar.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 *
	 * @return  string   The date string in the specified format format.
	 *
	 * @since   11.1
	 */
	public function calendar($format, $local = false, $translate = true, $lunar_day_setting = 0)
	{
		self::$lunar_day_setting = $lunar_day_setting;

		// Do string replacements for date format options that can be translated.
		$format = preg_replace('/(^|[^\\\])d/', "\\1".self::DAY_NUMBER2, $format);
		$format = preg_replace('/(^|[^\\\])j/', "\\1".self::DAY_NUMBER, $format);
		$format = preg_replace('/(^|[^\\\])z/', "\\1".self::DAY_YEAR, $format);
		$format = preg_replace('/(^|[^\\\])M/', "\\1".self::MONTH_ABBR, $format);
		$format = preg_replace('/(^|[^\\\])F/', "\\1".self::MONTH_NAME, $format);
		$format = preg_replace('/(^|[^\\\])n/', "\\1".self::MONTH_NUMBER, $format);
		$format = preg_replace('/(^|[^\\\])m/', "\\1".self::MONTH_NUMBER2, $format);
		$format = preg_replace('/(^|[^\\\])t/', "\\1".self::MONTH_LENGTH, $format);
		$format = preg_replace('/(^|[^\\\])y/', "\\1".self::YEAR_ABBR, $format);
		$format = preg_replace('/(^|[^\\\])Y/', "\\1".self::YEAR_NAME, $format);
		$format = preg_replace('/(^|[^\\\])a/', "\\1".self::AM_LOWER, $format);
		$format = preg_replace('/(^|[^\\\])A/', "\\1".self::AM_UPPER, $format);

		// Format the date.
		$return = parent::calendar($format, $local);

		$jd        = gregoriantojd($this->month, $this->day, $this->year);
		$lunarDate = self::jdtolunar($jd);
		$m         = $lunarDate['mon'];
		$d         = $lunarDate['day'];
		$y         = $lunarDate['year'];

		// Manually modify the strings in the formated time.
		if (strpos($return, self::DAY_NUMBER) !== false)
		{
			$return = str_replace(self::DAY_NUMBER, $d , $return);
		}
		if (strpos($return, self::DAY_NUMBER2) !== false)
		{
			$return = str_replace(self::DAY_NUMBER2, sprintf("%02d",$d), $return);
		}
		if (strpos($return, self::DAY_YEAR) !== false)
		{
			$return = str_replace(self::DAY_YEAR, $jd - self::lunartojd(1,1,$y)+1, $return);
		}
		if (strpos($return, self::MONTH_ABBR) !== false)
		{
			$return = str_replace(self::MONTH_ABBR, $this->monthToString($m, true), $return);
		}
		if (strpos($return, self::MONTH_NAME) !== false)
		{
			$return = str_replace(self::MONTH_NAME, $this->monthToString($m), $return);
		}
		if (strpos($return, self::MONTH_NUMBER) !== false)
		{
			$return = str_replace(self::MONTH_NUMBER, $m , $return);
		}
		if (strpos($return, self::MONTH_NUMBER2) !== false) {
			$return = str_replace(self::MONTH_NUMBER2, sprintf("%02d", $m) , $return);
		}
		if (strpos($return, self::MONTH_LENGTH) !== false)
		{
			$return = str_replace(self::MONTH_LENGTH, ($m == 12) && self::leap_lunar($y) ? 30 : $m % 2 ? 30 : 29 , $return);
		}
		if (strpos($return, self::YEAR_ABBR) !== false)
		{
			$return = str_replace(self::YEAR_ABBR, sprintf("%02d",$y % 100), $return);
		}
		if (strpos($return, self::YEAR_NAME) !== false)
		{
			$return = str_replace(self::YEAR_NAME, $y, $return);
		}
		if (strpos($return, self::AM_LOWER) !== false)
		{
			$return = str_replace(self::AM_LOWER, $this->format('a',$local)=='pm' ? JText::_('PM_SHORT') : JText::_('AM_SHORT'), $return);
		}
		if (strpos($return, self::AM_UPPER) !== false)
		{
			$return = str_replace(self::AM_UPPER, $this->format('a',$local)=='pm' ? JText::_('PM') : JText::_('AM'), $return);
		}

		return $return;
	}

	public static function jdtolunar($jd)
	{
		$jd    = floor($jd) + 0.5;
		$year  = floor(((30 * ($jd - self::LUNAR_EPOCH)) + 10646) / 10631);
		$month = min(12, ceil(($jd - (29 + self::lunartojd(1, 1, $year))) / 29.5) + 1);
		$day   = ($jd - self::lunartojd($month, 1, $year)) + self::$lunar_day_setting;

		return array('year'=>$year, 'mon'=>$month,'day'=> $day);
	}

	public static function lunartojd($month, $day, $year)
	{
		return ($day +
			ceil(29.5 * ($month - 1)) +
			($year - 1) * 354 +
			floor((3 + (11 * $year)) / 30) +
			self::LUNAR_EPOCH);
	}

	public static function leap_lunar($year)
	{
		return ((($year * 11) + 14) % 30) < 11;
	}
}