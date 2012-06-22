<?php
/***************************************************************
* Copyright notice
*
* (c) 2007-2012 Niels Pardon (mail@niels-pardon.de)
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

/**
 * Class 'tx_seminars_registrationchild' for the 'seminars' extension.
 *
 * This is mere a class used for unit tests of the 'seminars' extension. Don't
 * use it for any other purpose.
 *
 * @package TYPO3
 * @subpackage tx_seminars
 *
 * @author Niels Pardon <mail@niels-pardon.de>
 */
class tx_seminars_registrationchild extends tx_seminars_registration {
	/**
	 * The constructor.
	 *
	 * @param integer $registrationUid UID of the registration record, must be > 0
	 */
	public function __construct($registrationUid = 0) {
		if ($registrationUid > 0) {
			$dbResult = tx_oelib_db::select(
				'*',
				$this->tableName,
				'uid = ' . $registrationUid
			);
		} else {
			$dbResult = FALSE;
		}

		/** @var $contentObject tslib_cObj */
		$contentObject = t3lib_div::makeInstance('tslib_cObj');
		$contentObject->start(array());

		parent::__construct($contentObject, $dbResult);
	}

	/**
	 * Sets the "registration_queue" field of the registration record.
	 *
	 * @param boolean $isOnRegistrationQueueValue TRUE if the registration should be on the waiting list, FALSE otherwise
	 */
	public function setIsOnRegistrationQueue($isOnRegistrationQueueValue) {
		$this->setRecordPropertyInteger(
			'registration_queue',
			intval($isOnRegistrationQueueValue)
		);
	}

	/**
	 * Sets the payment method of this registration.
	 *
	 * @param integer $uid the UID of the payment method to set
	 */
	public function setPaymentMethod($uid) {
		if ($uid <= 0) {
			throw new InvalidArgumentException('Invalid payment method UID.', 1333293343);
		}

		$this->setRecordPropertyInteger(
			'method_of_payment',
			$uid
		);
	}

	/**
	 * Sets the data of the FE user of this registration.
	 *
	 * @param array $userData data of the front-end user, may be empty
	 */
	public function setUserData(array $userData) {
		parent::setUserData($userData);
	}

	/**
	 * Returns the content of the member variable foods.
	 *
	 * @return array the content of the member variable foods, will be empty if
	 *               foods is empty
	 */
	public function getFoodsData() {
		return $this->foods;
	}

	/**
	 * Returns the content of the member variable lodgings.
	 *
	 * @return array the content of the member variable lodgings, will be empty
	 *               if lodgings is empty
	 */
	public function getLodgingsData() {
		return $this->lodgings;
	}

	/**
	 * Returns the content of the member variable checkboxes.
	 *
	 * @return array the content of the member variable checkboxes, will be
	 *               empty if checkboxes is empty
	 */
	public function getCheckboxesData() {
		return $this->checkboxes;
	}

	/**
	 * Sets the value for 'registered_themselves'.
	 *
	 * @param boolean $registeredThemselves the value for the registered_themselves
	 *                property
	 */
	public function setRegisteredThemselves($registeredThemselves) {
		$this->setRecordPropertyBoolean('registered_themselves', $registeredThemselves);
	}
}
?>