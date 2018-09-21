<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

/**
 * Description of RecipientEmail
 *
 * @author adm.e.lisin
 */
class RecipientEmail {
 
    private $isActive;
    private $email;
    public function __construct($arrayInfo) {
        
        $this->isActive = ($arrayInfo['ACTIVE'] == 'Y');
        $this->email = $arrayInfo['EMAIL'];
    }
    
    function isActive() {
        return $this->isActive;
    }
    
    function getEmail() {
        return $this->email;
    }


    
    
}
