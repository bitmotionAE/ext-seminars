<?php
namespace OliverKlee\Seminars\Service;

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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * This class takes care of sending emails.
 *
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class EmailService implements SingletonInterface
{
    /**
     * @var \Tx_Seminars_EmailSalutation
     */
    protected $salutationBuilder = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->salutationBuilder = GeneralUtility::makeInstance(\Tx_Seminars_EmailSalutation::class);
    }

    /**
     * Sends an email to of registered users of the given event.
     *
     * @param \Tx_Seminars_Model_Event $event
     * @param string $subject
     * @param string $body can contain %salutation which will expand to a full salutation with the user's name
     *
     * @return void
     */
    public function sendEmailToAttendees(\Tx_Seminars_Model_Event $event, $subject, $body)
    {
        /** @var \Tx_Oelib_MailerFactory $mailerFactory */
        $mailerFactory = GeneralUtility::makeInstance(\Tx_Oelib_MailerFactory::class);
        $mailer = $mailerFactory->getMailer();

        /** @var \Tx_Seminars_Model_Organizer $firstOrganizer */
        $firstOrganizer = $event->getOrganizers()->first();

        /** @var \Tx_Seminars_Model_Registration $registration */
        foreach ($event->getRegistrations() as $registration) {
            $user = $registration->getFrontEndUser();
            if ($user === null || !$user->hasEmailAddress()) {
                continue;
            }

            /** @var \Tx_Oelib_Mail $eMail */
            $eMail = GeneralUtility::makeInstance(\Tx_Oelib_Mail::class);
            $eMail->setSender($firstOrganizer);
            $eMail->addRecipient($registration->getFrontEndUser());
            $eMail->setSubject($subject);

            $bodyWithFooter = $this->buildMessageBody($body, $firstOrganizer, $user);
            $eMail->setMessage($bodyWithFooter);

            $mailer->send($eMail);
        }
    }

    /**
     * Builds the message body (including the email footer).
     *
     * @param string $rawBody
     * @param \Tx_Seminars_Model_Organizer $organizer
     * @param \Tx_Seminars_Model_FrontEndUser $user
     *
     * @return string
     */
    protected function buildMessageBody(
        $rawBody,
        \Tx_Seminars_Model_Organizer $organizer,
        \Tx_Seminars_Model_FrontEndUser $user
    ) {
        $salutation = $this->salutationBuilder->getSalutation($user);
        $salutationKey = '%' . $this->getLanguageService()->getLL('mailForm_salutation');
        $bodyWithReplaceSalutation = str_replace($salutationKey, $salutation, $rawBody);

        $bodyWithFooter = $bodyWithReplaceSalutation;
        if ($organizer->hasEMailFooter()) {
            $bodyWithFooter .= LF . '-- ' . LF . $organizer->getEMailFooter();
        }

        return $bodyWithFooter;
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
}
