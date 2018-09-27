<?php


namespace f00x\AntiBitrix;

/**
 * Description of BitrixForm
 *
 * @author adm.e.lisin
 */
class Form {

    /**
     *
     * @var FormField[] 
     */
    private $listField = [];
    private $id;
    private $title;
    private $SID;
    private $isCaptcha;
    private $mailEventKey;
    private $isSubmitted;
    private $isAllFieldsValid;
    private $CapchaObject;

    public function __construct($arrayForm, $arrayFormFields) {
        $this->id = $arrayForm['ID'];
        $this->title = $arrayForm['NAME'];
        $this->SID = $arrayForm['SID'];
        $this->isCaptcha = ($arrayForm['USE_CAPTCHA'] == 'Y');
        $this->mailEventKey = $arrayForm['MAIL_EVENT_TYPE'];
        $this->addFieldsByArray($arrayFormFields);
    }

    public function eachField() {
        yield 'FORM_ID' => $this->id;
        yield 'FORM_SID' => $this->SID;
        yield 'FORM_TITLE' => $this->title;
        foreach ($this->listField as $Field) {
            yield $Field->getSID() => $Field->getValue();
        }
    }

    private function addFieldsByArray($arrayFormFields) {
        foreach ($arrayFormFields as $FormField) {
            $this->listField[] = new FormField($FormField);
        }
    }

    public function handleRequestPOST($POST) {
        if ($POST['WEB_FORM_ID'] == $this->id)
            $this->isSubmitted = true;

        $this->testValidAllFields($POST);
    }

    private function testValidAllFields($POST) {
        $this->isAllFieldsValid = true;
        foreach ($this->listField as $Field) {
            $nameField = $Field->getName();
            $value = isset($POST[$nameField]) ? $POST[$nameField] : '';


            if ($Field->isValidValue($value)) {
                $Field->setValue($value);
            } else {
                $this->isAllFieldsValid = false;
            }
        }
        return $this->isAllFieldsValid;
    }

    public function isSubmitted() {
        return $this->isSubmitted;
    }

    public function isValid() {
        return ($this->isSubmitted && $this->isAllFieldsValid);
    }

    function getTitle() {
        return $this->title;
    }

    function getSID() {
        return $this->SID;
    }

    function isCaptcha() {
        return $this->isCaptcha;
    }

    function getMailEventKey() {
        return $this->mailEventKey;
    }

    function getListField(): array {
        return $this->listField;
    }
    function getId() {
        return $this->id;
    }


}
