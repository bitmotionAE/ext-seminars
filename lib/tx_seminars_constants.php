<?php
/***************************************************************
* Copyright notice
*
* (c) 2007-2008 Oliver Klee (typo3-coding@oliverklee.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software); you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation); either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY); without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

// the UTF-8 representation of an en dash
define(UTF8_EN_DASH, chr(0xE2).chr(0x80).chr(0x93));
// a tabulator
define('TAB', chr(9));
// a linefeed
define('LF', chr(10));
// a carriage return
define('CR', chr(13));
// a CR-LF combination (the default Unix line ending)
define('CRLF', CR.LF);
// one day in seconds
define('ONE_DAY', 86400);
// one week in seconds
define('ONE_WEEK', 604800);

define('SEMINARS_TABLE_SEMINARS', 'tx_seminars_seminars');
define('SEMINARS_TABLE_SPEAKERS', 'tx_seminars_speakers');
define('SEMINARS_TABLE_SITES', 'tx_seminars_sites');
define('SEMINARS_TABLE_ORGANIZERS', 'tx_seminars_organizers');
define('SEMINARS_TABLE_ATTENDANCES', 'tx_seminars_attendances');
define('SEMINARS_TABLE_PAYMENT_METHODS', 'tx_seminars_payment_methods');
define('SEMINARS_TABLE_EVENT_TYPES', 'tx_seminars_event_types');
define('SEMINARS_TABLE_CHECKBOXES', 'tx_seminars_checkboxes');
define('SEMINARS_TABLE_LODGINGS', 'tx_seminars_lodgings');
define('SEMINARS_TABLE_FOODS', 'tx_seminars_foods');
define('SEMINARS_TABLE_TIME_SLOTS', 'tx_seminars_timeslots');
define('SEMINARS_TABLE_TARGET_GROUPS', 'tx_seminars_target_groups');
define('SEMINARS_TABLE_CATEGORIES', 'tx_seminars_categories');
define('SEMINARS_TABLE_SKILLS', 'tx_seminars_skills');
define('SEMINARS_TABLE_TEST', 'tx_seminars_test');

define('SEMINARS_TABLE_VIPS_MM', 'tx_seminars_seminars_feusers_mm');
define('SEMINARS_TABLE_SPEAKERS_MM', 'tx_seminars_seminars_speakers_mm');
define('SEMINARS_TABLE_PARTNERS_MM', 'tx_seminars_seminars_speakers_mm_partners');
define('SEMINARS_TABLE_TUTORS_MM', 'tx_seminars_seminars_speakers_mm_tutors');
define('SEMINARS_TABLE_LEADERS_MM', 'tx_seminars_seminars_speakers_mm_leaders');
define('SEMINARS_TABLE_SITES_MM', 'tx_seminars_seminars_place_mm');
define('SEMINARS_TABLE_SEMINARS_CHECKBOXES_MM', 'tx_seminars_seminars_checkboxes_mm');
define('SEMINARS_TABLE_ATTENDANCES_CHECKBOXES_MM', 'tx_seminars_attendances_checkboxes_mm');
define('SEMINARS_TABLE_SEMINARS_LODGINGS_MM', 'tx_seminars_seminars_lodgings_mm');
define('SEMINARS_TABLE_ATTENDANCES_LODGINGS_MM', 'tx_seminars_attendances_lodgings_mm');
define('SEMINARS_TABLE_SEMINARS_FOODS_MM', 'tx_seminars_seminars_foods_mm');
define('SEMINARS_TABLE_ATTENDANCES_FOODS_MM', 'tx_seminars_attendances_foods_mm');
define('SEMINARS_TABLE_TIME_SLOTS_SPEAKERS_MM', 'tx_seminars_timeslots_speakers_mm');
define('SEMINARS_TABLE_TARGET_GROUPS_MM', 'tx_seminars_seminars_target_groups_mm');
define('SEMINARS_TABLE_CATEGORIES_MM', 'tx_seminars_seminars_categories_mm');
define('SEMINARS_TABLE_SPEAKERS_SKILLS_MM', 'tx_seminars_speakers_skills_mm');
define('SEMINARS_TABLE_ORGANIZING_PARTNERS_MM', 'tx_seminars_seminars_organizing_partners_mm');

define('SEMINARS_RECORD_TYPE_COMPLETE', 0);
define('SEMINARS_RECORD_TYPE_TOPIC', 1);
define('SEMINARS_RECORD_TYPE_DATE', 2);

?>
