<?php

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
    private $SettingsArray = [];

    /**
     * @var Swift_Mailer
     */
    private $SmtpMailer;

    public function __construct(array $SettingsArray) {
        if (isset($SettingsArray['anti_bitrix']['mysql']) && is_array($SettingsArray['anti_bitrix']['mysql'])) {
            $dbArrayConfig = $SettingsArray['anti_bitrix']['mysql'];
        } else if (isset($SettingsArray['connections']['value']['default']) && is_array($SettingsArray['connections']['value']['default'])) {
            $dbArrayConfig = $SettingsArray['connections']['value']['default'];
        }
        $this->setDBconfig($dbArrayConfig);
        if (isset($SettingsArray['anti_bitrix']) && is_array($SettingsArray['anti_bitrix'])) {
            $this->SettingsArray = $SettingsArray['anti_bitrix'];
        }
        if (isset($SettingsArray['anti_bitrix']['smtp']) && !empty($SettingsArray['anti_bitrix']['smtp'])) {
            $this->setSmtpConfig($SettingsArray['anti_bitrix']['smtp']);
        } else {
            $this->setSmtpConfig();
        }
    }

    public function isReCaptcha() {
        return isset($this->SettingsArray['re_captcha']['client_key']);
    }

    public function __call($nameFunction, $arrayArguments) {
        $snakeName = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $nameFunction)), '_');
        $splitArray = preg_split('/_/', $snakeName);
        if ($splitArray[0] == 'get') {
            $result = false;
            $stage = $this->SettingsArray;
            $stageKey = '';
            foreach ($splitArray as $key) {
                if ($key == 'get') {
                    continue;
                }
                $stageKey .= $key;
                if (isset($stage[$stageKey])) {
                    $stage = $stage[$stageKey];
                    $stageKey = '';
                } else {
                    $stageKey .= '_';
                }
            }
            if ($stage === $this->SettingsArray) {
                return false;
            }else {return $stage;}
        }
    }

    private function setSmtpConfig($param = false) {
        if (is_array($param)) {
            $transport = (new Swift_SmtpTransport($param['host'], $param['port']))
                    ->setUsername($param['username'])
                    ->setPassword($param['password'])
                    ->setEncryption($param['encryption'])
            ;
            if (isset($param['is_ntlm']) && $param['is_ntlm']) {
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

    public function getCaptcha() {
        
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
