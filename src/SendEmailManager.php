<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

/**
 * Description of SendEmailManager
 *
 * @author adm.e.lisin
 */
class SendEmailManager {

    private $DB;
    private $currentLanguageCode;

    /**
     * @var \PDOStatement
     */
    private $StatementEmailTemplate;
    private $currentCodeEmailTemplate;

    /**
     * @var \PDOStatement
     */
    private $StatementSubscriptionEmail;
    private $currentCodeSubscriptionEmail;

    /**
     * @var \PDOStatement
     */
    private $StatementSubscriptionListEmail;
    private $currentIdSubscriptionEmail;

    public function __construct(\PDO $DB, $langCode = 'ru') {
        $this->DB = $DB;
        $this->currentLanguageCode = $langCode;
    }

    /**
     * @return \PDOStatement
     */
    private function getStatementEmailTemplate() {
        if (!$this->StatementEmailTemplate) {
            $stmt = $this->DB->prepare('SELECT `b_event_message`.* 
                FROM `b_event_message` left join  `b_lang` on  `b_lang`.`LID`=`b_event_message`.`LID`
                    where `b_lang`.`LANGUAGE_ID`=:lang_code 
                    and `b_event_message`.`EVENT_NAME`=:code');
            assert($stmt instanceof \PDOStatement);
            $stmt->bindParam(':code', $this->currentCodeEmailTemplate);
            $stmt->bindParam(':lang_code', $this->currentLanguageCode);
            $this->StatementEmailTemplate = $stmt;
        }
        return $this->StatementEmailTemplate;
    }

    /**
     * @return \PDOStatement
     */
    private function getStatementSubscriptionEmail() {
        if (!$this->StatementSubscriptionEmail) {
            $stmt = $this->DB->prepare('SELECT * FROM `b_list_rubric` WHERE `CODE`=:code');
            assert($stmt instanceof \PDOStatement);
            $stmt->bindParam(':code', $this->currentCodeSubscriptionEmail);
            $this->StatementSubscriptionEmail = $stmt;
        }
        return $this->StatementSubscriptionEmail;
    }

    /**
     * @return \PDOStatement
     */
    private function getStatementSubscriptionListEmail() {
        if (!$this->StatementSubscriptionListEmail) {
            $stmt = $this->DB->prepare('SELECT `b_subscription`.* '
                    . 'FROM `b_subscription` '
                    . 'inner join `b_subscription_rubric` on `b_subscription_rubric`.`SUBSCRIPTION_ID`= `b_subscription`.`ID` '
                    . 'WHERE `LIST_RUBRIC_ID`=:id');
            assert($stmt instanceof \PDOStatement);
            $stmt->bindParam(':id', $this->currentIdSubscriptionEmail);
            $this->StatementSubscriptionListEmail = $stmt;
        }
        return $this->StatementSubscriptionListEmail;
    }

    /**
     * 
     * @param type $codeEmailTemplate
     * @return \f00x\AntiBitrix\TemplateEmail
     * @throws \Exception
     */
    public function getTemplateEmail($codeEmailTemplate) {
        $this->currentCodeEmailTemplate = $codeEmailTemplate;

        if ($this->getStatementEmailTemplate()->execute()) {
            $arrayInfo = $this->getStatementEmailTemplate()->fetch();
            return new TemplateEmail($arrayInfo);
        } else {
            throw new \Exception(__CLASS__ . ' ' . __METHOD__ . ' Error PDOStatement Execute codeErrorPDO= ' . $this->getStatementEmailTemplate()->errorCode() . '  ' . $this->getStatementEmailTemplate()->errorInfo()[2]);
        }
    }

    /**
     * 
     * @param type $codeSubscriptionEmail
     * @return \f00x\AntiBitrix\SubscriptionEmail
     * @throws \Exception
     */
    public function getSubscriptionEmail($codeSubscriptionEmail) {
        $this->currentCodeSubscriptionEmail = $codeSubscriptionEmail;

        if ($this->getStatementSubscriptionEmail()->execute()) {

            $arrayInfo = $this->getStatementSubscriptionEmail()->fetch();
            $this->currentIdSubscriptionEmail = $arrayInfo['ID'];
            $listEmailInfo = [];
            if ($this->getStatementSubscriptionListEmail()->execute()) {

                $listEmailInfo = $this->getStatementSubscriptionListEmail()->fetchAll();
            }
            return new SubscriptionEmail($arrayInfo, $listEmailInfo);
        } else {
            throw new \Exception(__CLASS__ . ' ' . __METHOD__ . ' Error PDOStatement Execute codeErrorPDO= ' . $this->getStatemenForm()->errorCode() . ' and ' . $this->getStatementFieldList()->errorCode());
        }
    }

    public function sendMsgForm(Form $Form) {
        $TemplateEmail = $this->getTemplateEmail($Form->getMailEventKey());
        $SubscriptionEmail = $this->getSubscriptionEmail($Form->getMailEventKey());
        if ($SubscriptionEmail->isActive()) {
            $TemplateEmail->applyFormData($Form);
            $this->sendEmail($SubscriptionEmail->getStringRecipientEmail(), $TemplateEmail->getTextSubject(), $TemplateEmail->getTextMessage(), $this->getHeadersMsgString($TemplateEmail, $SubscriptionEmail));
        }
    }
    private function sendEmail($to,$subject,$message,$headers)
    {
        mail($to, $subject, $message,$headers);
        
    }
    private function getHeadersMsgString(TemplateEmail $TemplateEmail, SubscriptionEmail $SubscriptionEmail) {
        $to = $SubscriptionEmail->getStringRecipientEmail();
        $from=$SubscriptionEmail->getEmailFrom();
        $reply=$TemplateEmail->getEmailReply();
        $mimeType = $TemplateEmail->getMimeType();
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: '.$mimeType.'; charset=UTF-8',
            'To: ' . $to,
            'From: ' .$from
        ];
        
        if(!empty($reply)){
            $headers[]='Reply-To: '.$reply;
        }
        return implode("\r\n",$headers);
    }

}
