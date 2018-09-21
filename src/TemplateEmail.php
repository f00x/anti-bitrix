<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

/**
 * Description of TemplateEmail
 *
 * @author adm.e.lisin
 */
class TemplateEmail {

    const TYPE_KEY_HTML = 'html';
    const TYPE_KEY_TEXT = 'text';

    /**
     * @var string
     */
    private $templateSubject;

    /**
     *
     * @var string
     */
    private $templateMessage;
    private $listAllParameter;
    protected $RegularExpressionKeyParameter;
    private $mimeType;
    private $emailReply;
    public function __construct($arrayGroupTemplate) {

        $this->setType($arrayGroupTemplate['BODY_TYPE']);
        $this->templateSubject = $arrayGroupTemplate['SUBJECT'];
        $this->templateMessage = $arrayGroupTemplate['MESSAGE'];
         $this->emailReply=$arrayGroupTemplate['REPLY_TO'];
        $this->initEmtiListParameter();
    }

    private function setType($key) {
        if($key==self::TYPE_KEY_HTML){
            $this->mimeType='text/html';
        }else{
            $this->mimeType='text/plain';
        }
    }
    function getMimeType() {
        return $this->mimeType;
    }

    public function getTextSubject() {
        $tt = $this->getArrayRegularExpressionKeyParameter();
        return preg_replace($this->getArrayRegularExpressionKeyParameter(), $this->listAllParameter, $this->templateSubject);
    }

    public function getTextMessage() {
        return preg_replace($this->getArrayRegularExpressionKeyParameter(), $this->listAllParameter, $this->templateMessage);
    }

    private function getArrayRegularExpressionKeyParameter() {
        if (!is_array($this->RegularExpressionKeyParameter)) {
            $this->RegularExpressionKeyParameter = array_keys($this->listAllParameter);

            foreach ($this->RegularExpressionKeyParameter as &$key) {
                $key = $this->keyToRegular($key);
            }
        }
        return $this->RegularExpressionKeyParameter;
    }

    private function keyToRegular($key) {
        return '/#' . $key . '#/i';
    }

    public function applyFormData(Form $Form) {
        foreach ($Form->eachField() as $key => $value) {
            $this->setParameter($key, $value);
        }
    }

    public function setParameter($key, $value) {
        $key = strtoupper($key);
        $this->listAllParameter[$key] = $value;
    }

    public function getAllParams() {
        return $this->listAllParameter;
    }

    private function initEmtiListParameter() {
        $this->listAllParameter = $this->getEmtyParamArrayInText($this->templateSubject);
        $this->listAllParameter = array_merge($this->listAllParameter, $this->getEmtyParamArrayInText($this->templateMessage));
        $this->listAllParameter = array_change_key_case($this->listAllParameter, CASE_UPPER);
        $intersectServerVariable = array_intersect_key($_SERVER, $this->listAllParameter);
        $this->listAllParameter = array_merge($this->listAllParameter, $intersectServerVariable);
    }

    private function getEmtyParamArrayInText($string) {
        preg_match_all('/\#(\w+)\#/', $string, $MachArray);
        return array_fill_keys($MachArray[1], '');
    }
    public function getEmailReply() {
        return $this->emailReply;
    }

}
