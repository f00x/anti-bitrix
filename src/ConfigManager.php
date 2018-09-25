<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

use PDO;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_SmtpTransport;
use Swift_Transport_Esmtp_Auth_NTLMAuthenticator;
/**
 * Description of BitrixConfigManager
 *
 * @author adm.e.lisin
 */
class ConfigManager {

    private $DBLogin;
    private $DBPassword;
    private $DBName;
    private $DBHost;
    private $MysqlPDOConnect;

    /**
     *
     * @var Swift_Mailer
     */
    private $SmtpMailer;

    public function __construct(array $settingsBitrix) {
        if (isset($settingsBitrix['anti_bitrix']['mysql']) && is_array($settingsBitrix['anti_bitrix']['mysql'])) {
            $dbArrayConfig = $settingsBitrix['anti_bitrix']['mysql'];
        } else if (isset($settingsBitrix['connections']['value']['default']) && is_array($settingsBitrix['connections']['value']['default'])) {
            $dbArrayConfig = $settingsBitrix['connections']['value']['default'];
        }
        $this->setDBconfig($dbArrayConfig);
        if (isset($settingsBitrix['anti_bitrix']['smtp']) && !empty($settingsBitrix['anti_bitrix']['smtp'])) {
            $this->setSmtpConfig($settingsBitrix['anti_bitrix']['smtp']);
        } else {
            $this->setSmtpConfig();
        }
    }

    private function setSmtpConfig($param = false) {
        if (is_array($param)) {
            $transport = (new Swift_SmtpTransport($param['host'],$param['port']))
                    ->setUsername($param['username'])
                    ->setPassword($param['password'])
                    ->setEncryption($param['encryption'])
            ;
            if(isset($param['is_ntlm'])&& $param['is_ntlm']){
            $transport->setAuthenticators([new Swift_Transport_Esmtp_Auth_NTLMAuthenticator()]);
            }
            
            $this->SmtpMailer = new Swift_Mailer($transport);
        } else {
            $transport = new Swift_MailTransport();
            $this->SmtpMailer = new Swift_Mailer($transport);
        }
    }

    private function setDBconfig($settingsDB) {
        $this->DBLogin = $settingsDB['login'];
        $this->DBPassword = $settingsDB['password'];
        $this->DBName = $settingsDB['database'];
        $this->DBHost = $settingsDB['host'];
    }

    public function getMysqlPDOConnect() {
        if (!$this->MysqlPDOConnect instanceof PDO) {
            $this->MysqlPDOConnect = new PDO('mysql:host=' . $this->DBHost . ';dbname=' . $this->DBName, $this->DBLogin, $this->DBPassword);
            $this->MysqlPDOConnect->exec(" SET CHARACTER SET 'utf8';");
        }
        return $this->MysqlPDOConnect;
    }

    /**
     * 
     * @return Swift_Mailer
     */
    function getSmtpMailer() {
        return $this->SmtpMailer;
    }

    //put your code here
}
