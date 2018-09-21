<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

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

    public function __construct(array $settingsBitrix) {
        $this->DBLogin = $settingsBitrix['connections']['value']['default']['login'];
        $this->DBPassword = $settingsBitrix['connections']['value']['default']['password'];
        $this->DBName = $settingsBitrix['connections']['value']['default']['database'];
        $this->DBHost = $settingsBitrix['connections']['value']['default']['host'];
    }

    public function getMysqlPDOConnect() {
        if (!$this->MysqlPDOConnect instanceof \PDO) {
            $this->MysqlPDOConnect = new \PDO('mysql:host=' . $this->DBHost . ';dbname=' . $this->DBName, $this->DBLogin, $this->DBPassword);
        $this->MysqlPDOConnect->exec(" SET CHARACTER SET 'utf8';");
            
        }
        return $this->MysqlPDOConnect;
    }

    //put your code here
}
