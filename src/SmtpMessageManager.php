<?php

/*
 * Copyright (C) 2018 adm.e.lisin
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace f00x\AntiBitrix;

use Swift_Message;



/**
 * Description of SmtpMessageManager
 *
 * @author adm.e.lisin
 */
class SmtpMessageManager implements MessageManagerInterface {

    use TraitBaseMessageManager;

    private function getMessage(TemplateEmail $TemplateEmail, SubscriptionEmail $SubscriptionEmail) {
        $Message = new Swift_Message();
      
        $Message->setFrom($SubscriptionEmail->getEmailFrom());
        $Message->setSubject($TemplateEmail->getTextSubject());
        $Message->setReturnPath($TemplateEmail->getEmailReply());
        foreach ($SubscriptionEmail->eachRecipientEmail() as $emailTo) {
            $Message->addTo($emailTo);
        }
       
            $Message->setBody($TemplateEmail->getTextMessage(),$TemplateEmail->getMimeType());
     
        return $Message;
    }

    public function sendMsgForm(Form $Form) {
        $TemplateEmail = $this->getTemplateEmail($Form->getMailEventKey());
        $SubscriptionEmail = $this->getSubscriptionEmail($Form->getMailEventKey());
        if ($SubscriptionEmail->isActive()) {
            $TemplateEmail->applyFormData($Form);
            $MsgObject = $this->getMessage($TemplateEmail, $SubscriptionEmail);

            $this->Smtp->send($MsgObject);
        }
    }

}
