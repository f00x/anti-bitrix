<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

/**
 * Description of FormField
 *
 * @author adm.e.lisin
 */
class FormField {
    private $id;
    private $title;
    private $typeKey;
    private $SID;
    private $isRequired;
    private $value;
    
    public function __construct($arrayFieldInfo) {
        $this->id=$arrayFieldInfo['ID'];
        $this->title=$arrayFieldInfo['TITLE'];
        $this->typeKey=$arrayFieldInfo['FIELD_TYPE'];
        $this->SID=$arrayFieldInfo['SID'];
        $this->isRequired=($arrayFieldInfo['REQUIRED']=='Y');
    }
    
    public function getName()
    { return 'form_'.$this->typeKey.'_'.$this->id;
    }
    public function isValidValue($value) {
        if($this->isRequired()&&empty($value))
        {
            return false;
        }
        return true;
    }
    function getTitle() {
        return $this->title;
    }

    function getTypeKey() {
        return $this->typeKey;
    }

    function getSID() {
        return $this->SID;
    }

    function isRequired() {
        return $this->isRequired;
    }
    function getValue() {
        return $this->value;
    }

    function setValue($value) {
        $this->value = $value;
    }



}
