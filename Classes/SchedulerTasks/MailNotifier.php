<?php
namespace OliverKlee\Seminars\SchedulerTasks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * This class sends reminders to the organizers.
 *
 * @author Saskia Metzler <saskia@merlin.owl.de>
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class MailNotifier extends AbstractTask
{
    /**
     * @var int
     */
    protected $configurationPageUid = 0;

    /**
     * Runs the task.
     *
     * @return bool true on successful execution, false on error
     */
    public function execute()
    {
        if ($this->configurationPageUid <= 0) {
            return false;
        }

        $this->sendEventTakesPlaceReminders();
        $this->sendCancellationDeadlineReminders();

        return true;
    }

    /**
     * Sets the UID of the page with the TS configuration for this task.
     *
     * @param int $pageUid
     *
     * @return void
     */
    public function setConfigurationPageUid($pageUid)
    {
        $this->configurationPageUid = $pageUid;
    }

    /**
     * Sends event-takes-place reminders to the corresponding organizers and
     * commits the flag for this reminder being sent to the database.
     *
     * @return void
     */
    public function sendEventTakesPlaceReminders()
    {
        foreach ($this->getEventsToSendEventTakesPlaceReminderFor() as $event) {
            $this->sendRemindersToOrganizers(
                $event, 'email_eventTakesPlaceReminder'
            );
            $event->setEventTakesPlaceReminderSentFlag();
            $event->commitToDb();
        }
    }

    /**
     * Sends cancellation deadline reminders to the corresponding organizers and
     * commits the flag for this reminder being sent to the database.
     *
     * @return void
     */
    public function sendCancellationDeadlineReminders()
    {
        foreach ($this->getEventsToSendCancellationDeadlineReminderFor() as $event) {
            $this->sendRemindersToOrganizers(
                $event, 'email_cancelationDeadlineReminder'
            );
            $event->setCancelationDeadlineReminderSentFlag();
            $event->commitToDb();
        }
    }

    /**
     * Sends an e-mail to the organizers of the provided event.
     *
     * @param \Tx_Seminars_OldModel_Event $event event for which to send the reminder to its organizers
     * @param string $messageKey locallang key for the message content and the subject for the e-mail to send, must not be empty
     *
     * @return void
     */
    private function sendRemindersToOrganizers(\Tx_Seminars_OldModel_Event $event, $messageKey)
    {
        $attachment = null;

        // The first organizer is taken as sender.
        /** @var \Tx_Seminars_OldModel_Organizer $sender */
        $sender = $event->getFirstOrganizer();
        $subject = $this->customizeMessage($messageKey . 'Subject', $event);
        if ($this->shouldCsvFileBeAdded($event)) {
            $attachment = $this->getCsv($event->getUid());
        }

        /** @var \Tx_Seminars_OldModel_Organizer $organizer */
        foreach ($event->getOrganizerBag() as $organizer) {
            /** @var \Tx_Oelib_Mail $eMail */
            $eMail = GeneralUtility::makeInstance(\Tx_Oelib_Mail::class);
            $eMail->setSender($sender);
            $eMail->setSubject($subject);
            $eMail->addRecipient($organizer);
            $eMail->setMessage($this->customizeMessage($messageKey, $event, $organizer->getName()));
            if ($attachment !== null) {
                $eMail->addAttachment($attachment);
            }

            /** @var \Tx_Oelib_MailerFactory $mailerFactory */
            $mailerFactory = GeneralUtility::makeInstance(\Tx_Oelib_MailerFactory::class);
            $mailerFactory->getMailer()->send($eMail);
        }
    }

    /**
     * Returns events in confirmed status which are about to take place and for
     * which no reminder has been sent yet.
     *
     * @return \Tx_Seminars_OldModel_Event[] events for which to send the event-takes-place reminder to
     *               their organizers, will be empty if there are none
     */
    private function getEventsToSendEventTakesPlaceReminderFor()
    {
        $days = $this->getDaysBeforeBeginDate();
        if ($days == 0) {
            return array();
        }

        $result = array();

        $builder = $this->getSeminarBagBuilder(\Tx_Seminars_Model_Event::STATUS_CONFIRMED);
        $builder->limitToEventTakesPlaceReminderNotSent();
        $builder->limitToDaysBeforeBeginDate($days);
        $bag = $builder->build();

        /** @var \Tx_Seminars_OldModel_Event $event */
        foreach ($bag as $event) {
            $result[] = $event;
        }

        return $result;
    }

    /**
     * Returns events in planned status for which the cancellation deadline has
     * just passed and for which no reminder has been sent yet.
     *
     * @return \Tx_Seminars_OldModel_Event[] events for which to send the cancellation reminder to their
     *               organizers, will be empty if there are none
     */
    private function getEventsToSendCancellationDeadlineReminderFor()
    {
        if (!$this->getConfiguration()->getAsBoolean('sendCancelationDeadlineReminder')) {
            return array();
        }

        $result = array();

        /** @var $builder \Tx_Seminars_BagBuilder_Event */
        $builder = $this->getSeminarBagBuilder(\Tx_Seminars_Model_Event::STATUS_PLANNED);
        $builder->limitToCancelationDeadlineReminderNotSent();
        /** @var $bag \Tx_Seminars_Bag_Event */
        $bag = $builder->build();

        /** @var \Tx_Seminars_OldModel_Event $event */
        foreach ($bag as $event) {
            if ($event->getCancelationDeadline() < $GLOBALS['SIM_EXEC_TIME']) {
                $result[] = $event;
            }
        }

        return $result;
    }

    /**
     * Returns the TS setup configuration value of
     * 'sendEventTakesPlaceReminderDaysBeforeBeginDate'.
     *
     * @return int how many days before an event the event-takes-place
     *                 reminder should be send, will be > 0 if this option is
     *                 enabled, zero disables sending the reminder
     */
    private function getDaysBeforeBeginDate()
    {
        return $this->getConfiguration()->getAsInteger('sendEventTakesPlaceReminderDaysBeforeBeginDate');
    }

    /**
     * Returns a seminar bag builder already limited to upcoming events with a
     * begin date and status $status.
     *
     * @param int $status status to limit the builder to, must be either Tx_Seminars_Model_Event::STATUS_PLANNED or ::CONFIRMED
     *
     * @return \Tx_Seminars_BagBuilder_Event builder for the seminar bag
     */
    private function getSeminarBagBuilder($status)
    {
        /** @var \Tx_Seminars_BagBuilder_Event $builder */
        $builder = GeneralUtility::makeInstance(\Tx_Seminars_BagBuilder_Event::class);
        $builder->setTimeFrame('upcomingWithBeginDate');
        $builder->limitToStatus($status);

        return $builder;
    }

    /**
     * Returns the CSV output for the list of registrations for the event with the provided UID.
     *
     * @param int $eventUid UID of the event to create the output for, must be > 0
     *
     * @return \Tx_Oelib_Attachment CSV list of registrations for the given event
     */
    private function getCsv($eventUid)
    {
        /** @var \Tx_Seminars_Csv_EmailRegistrationListView $csvCreator */
        $csvCreator = GeneralUtility::makeInstance(\Tx_Seminars_Csv_EmailRegistrationListView::class);
        $csvCreator->setEventUid($eventUid);
        $csvString = $csvCreator->render();

        /** @var \Tx_Oelib_Attachment $attachment */
        $attachment = GeneralUtility::makeInstance(\Tx_Oelib_Attachment::class);
        $attachment->setContent($csvString);
        $attachment->setContentType('text/csv');
        $attachment->setFileName($this->getConfiguration()->getAsString('filenameForRegistrationsCsv'));

        return $attachment;
    }

    /**
     * Returns localized e-mail content customized for the provided event and
     * the provided organizer.
     *
     * @param string $locallangKey
     *        locallang key for the text in which to replace key words beginning with "%" by the event's data, must not be empty
     * @param \Tx_Seminars_OldModel_Event $event
     *        event for which to customize the text
     * @param string $organizerName
     *        name of the organizer, may be empty if no organizer name needs to be inserted in the text
     *
     * @return string the localized e-mail content, will not be empty
     */
    private function customizeMessage($locallangKey, \Tx_Seminars_OldModel_Event $event, $organizerName = '')
    {
        /** @var \Tx_Seminars_Model_BackEndUser $user */
        $user = \Tx_Oelib_BackEndLoginManager::getInstance()->getLoggedInUser(\Tx_Seminars_Mapper_BackEndUser::class);
        $languageService = $this->getLanguageService();
        $languageService->lang = $user->getLanguage();
        $languageService->includeLLFile('EXT:seminars/Resources/Private/Language/locallang.xml');
        $result = $languageService->getLL($locallangKey);

        foreach (array(
            '%begin_date' => $this->getDate($event->getBeginDateAsTimeStamp()),
            '%days' => $this->getDaysBeforeBeginDate(),
            '%event' => $event->getTitle(),
            '%organizer' => $organizerName,
            '%registrations' => $event->getAttendances(),
            '%uid' => $event->getUid(),
        ) as $search => $replace) {
            $result = str_replace($search, $replace, $result);
        }

        return $result;
    }

    /**
     * Returns a timestamp formatted according to the current configuration.
     *
     * @param int $timestamp timestamp, must be >= 0
     *
     * @return string formatted date according to the TS setup configuration for
     *                'dateFormatYMD', will not be empty
     */
    private function getDate($timestamp)
    {
        return strftime($this->getConfiguration()->getAsString('dateFormatYMD'), $timestamp);
    }

    /**
     * Checks whether the CSV file should be added to the e-mail.
     *
     * @param \Tx_Seminars_OldModel_Event $event the event to send the e-mail for
     *
     * @return bool TRUE if the CSV file should be added, FALSE otherwise
     */
    private function shouldCsvFileBeAdded(\Tx_Seminars_OldModel_Event $event)
    {
        return $this->getConfiguration()->getAsBoolean('addRegistrationCsvToOrganizerReminderMail')
            && $event->hasAttendances();
    }

    /**
     * Returns $GLOBALS['LANG'].
     *
     * @return LanguageService|null
     */
    protected function getLanguageService()
    {
        return isset($GLOBALS['LANG']) ? $GLOBALS['LANG'] : null;
    }

    /**
     * Returns the plugin.tx_seminars configuration.
     *
     * @return \Tx_Oelib_Configuration
     */
    protected function getConfiguration()
    {
        \Tx_Oelib_PageFinder::getInstance()->setPageUid($this->configurationPageUid);
        return \Tx_Oelib_ConfigurationRegistry::get('plugin.tx_seminars');
    }
}
