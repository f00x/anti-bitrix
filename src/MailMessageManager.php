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

/**
 * Description of SmtpMessageManager
 *
 * @author adm.e.lisin
 */
class MailMessageManager implements MessageManagerInterface {
   use  TraitBaseMessageManager;
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
    
    
    
}
