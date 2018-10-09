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
 * Description of ProxyForm
 *
 * @author adm.e.lisin
 */
class ProxyForm {

    private $ReCaptchaSiteKey;
    private $ReCaptchaSecredKey;
    private $DB;
    private $isSuccess = false;
    private $idFormProcessed;
    private $isValidCaptcha=false;
    /**
     *
     * @var Form[]
     */
    private $listForm = [];

    /**
     * 
     * @param string $KeyFormReCaptcha
     */
    public function __construct(ConfigManager $ConfigManager) {
        $this->ReCaptchaSiteKey = $ConfigManager->getReCaptchaSiteKey();
        $this->ReCaptchaSecredKey = $ConfigManager->getReCaptchaSecredKey();
        $this->DB = $ConfigManager->getMysqlPDOConnect();
       
    }

    public function getReCaptchaHtmlBlock() {
        $htmlText = <<<EOT
<div class = "g-recaptcha" data-sitekey = "#KEY_FORM_CAPTCHA#"></div> <script src = 'https://www.google.com/recaptcha/api.js'></script>
EOT;
        $htmlText = str_replace('#KEY_FORM_CAPTCHA#', $this->ReCaptchaSiteKey, $htmlText);
        return $htmlText;
    }

    public function isSuccess($idForm=false){
        if($idForm){
            if($idForm==$this->idFormProcessed){
             return  $this->isSuccess;  
            }
            return false;
        }
        return $this->isSuccess;
    }

    public function proxyPostDataForm() {
        foreach ($this->listForm as $Form) {
            $Form->handleRequestPOST($_POST);
            if ($Form->isSubmitted()) {
                $this->FormProcessing($Form);
                break;
            }
        }
    }

    public function getCaptchaValidator($POST) {
        if ($this->isReCaptcha()) {
            return new CaptchaValidatorGoogle($this->ReCaptchaSecredKey, '', $this->DB);
        } else {
            return new CaptchaValidatorStandart($POST["captcha_sid"], $POST["captcha_word"], $this->DB);
        }
    }

    /**
     * 
     * @return array
     */
    public function getListForm() {
        return $this->listForm;
    }

    public function addForm(Form $Form) {
        $this->listForm[] = $Form;
    }

    private function runEmitationStandartCaptchaSuccess() {
        $id_captcha = $this->getRandomString();
        $key_captcha = $this->getRandomString();
        //insert new item bd
        $stmt = $this->getStatementInsertBCaptcha();
        $stmt->bindParam(':id_capcha', $id_captcha);
        $stmt->bindParam(':key_capcha', $key_captcha);
        $stmt->execute();
        //set Post for bitrix
        $_POST['captcha_sid'] = $id_captcha;
        $_POST['captcha_word'] = $key_captcha;
    }

    private function runEmitationStandartCaptchaFalse() {
        $_POST['captcha_sid'] = microtime(true) . 'false';
        $_POST['captcha_word'] = microtime(true) . 'false';
    }

    private function getRandomString() {
        return (string) microtime(true) . md5('secretKey' . rand(0, 6000));
    }

    private function getStatementInsertBCaptcha() {
        $stmt = $this->DB->prepare('INSERT INTO `b_captcha`(`ID`, `CODE`, `IP`, `DATE_CREATE`) VALUES (:id_capcha,:key_capcha),\'127.0.0.1\',now())');
        return $stmt;
    }

    private function FormProcessing(Form $Form) {
        $this->idFormProcessed=$Form->getId();
        if ($Form->isValid()) {
            if ($Form->isCaptcha()) {
                if ($this->getCaptchaValidator($_POST)->isValid()) {
                    $this->runEmitationStandartCaptchaSuccess();
                     $this->isValidCaptcha=true;
                    $this->isSuccess = true;
                } else {
                    $this->runEmitationStandartCaptchaFalse();
                    $this->isSuccess = false;
                }
            } else {
                $this->isValidCaptcha=true;
                $this->isSuccess = true;
            }
        }
    }
    
    function isValidCaptcha() {
        return $this->isValidCaptcha;
    }

        
    
    
    
    
    
    

    private function isReCaptcha() {
        return ($this->ReCaptchaSiteKey && $this->ReCaptchaSecredKey);
    }

}
