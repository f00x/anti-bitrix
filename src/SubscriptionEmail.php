<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

/**
 * Description of SubscriptionEmail
 *
 * @author adm.e.lisin
 */
class SubscriptionEmail {

    private $name;
    private $description;
    private $code;
    private $isActive;
    private $emailFrom;
    
    private $TemplateEmail;

    /**
     *
     * @var RecipientEmail[]
     * 
     */
    private $listRecipientEmail = [];

    public function __construct($arraySubscryptionInfo, $arrayListRecipientEmail) {
        $this->name = $arraySubscryptionInfo['NAME'];
        $this->description = $arraySubscryptionInfo['DESCRIPTION'];
        $this->isActive = ($arraySubscryptionInfo['ACTIVE'] == 'Y');
        $this->setCode($arraySubscryptionInfo['CODE']);
        $this->emailFrom = $arraySubscryptionInfo['FROM_FIELD'];
       

        $this->addListRecipientEmail($arrayListRecipientEmail);
    }

    public function getStringRecipientEmail() {
        $emailListString='';
        foreach ($this->listRecipientEmail as $RecipientEmail) {
           $emailListString.=' '.$RecipientEmail->getEmail().',';
        }
        $emailListString =substr($emailListString,0,-1);
        return $emailListString;
    }

    private function addListRecipientEmail($arrayListRecipientEmail) {
        foreach ($arrayListRecipientEmail as $arrayRecipientEmail) {
            $this->listRecipientEmail[] = new RecipientEmail($arrayRecipientEmail);
        }
    }

    function getName() {
        return $this->name;
    }

    function getDescription() {
        return $this->description;
    }

    function getCode() {
        return $this->code;
    }

    function isActive() {
        return $this->isActive;
    }

    function setCode($code) {
        $this->code = trim(strtoupper($code));
    }

    function getEmailFrom() {
        return $this->emailFrom;
    }

    function getTemplateEmail() {
        return $this->TemplateEmail;
    }

    function setTemplateEmail($TemplateEmail) {
        $this->TemplateEmail = $TemplateEmail;
    }
   


}
