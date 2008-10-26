<?php
/***************************************************************
* Copyright notice
*
* (c) 2006-2008 Oliver Klee (typo3-coding@oliverklee.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('oelib') . 'class.tx_oelib_templatehelper.php');

/**
 * Class 'tx_seminars_configgetter' for the 'seminars' extension.
 *
 * This class provides a way to access config values from plugin.tx_seminars to
 * classes within pi1.
 *
 * @package TYPO3
 * @subpackage tx_seminars
 *
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class tx_seminars_configgetter extends tx_oelib_templatehelper {
	/** Same as class name */
	var $prefixId = 'tx_seminars_configgetter';
	/**  Path to this script relative to the extension dir. */
	var $scriptRelPath = 'class.tx_seminars_configgetter.php';

	/**
	 * @var string the extension key
	 */
	public $extKey = 'seminars';

	/**
	 * The constructor.
	 *
	 * @access public
	 */
	function __construct() {
		$this->init();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/seminars/class.tx_seminars_configgetter.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/seminars/class.tx_seminars_configgetter.php']);
}
?>