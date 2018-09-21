<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

/**
 * Description of BitrixFormManager
 *
 * @author adm.e.lisin
 */
class FormManager {

    /**
     * @var  \PDO
     */
    private $DB;
    private $currentIdForm;

    /**
     *
     * @var \PDOStatement
     */
    private $StatementForm;

    /**
     *
     * @var \PDOStatement
     */
    private $StatementFieldList;

    public function __construct(\PDO $DB) {
        $this->DB = $DB;
    }

  
/**
 * 
 * @param type $idForm
 * @return \f00x\AntiBitrix\Form
 * @throws \Exception
 */
    public function getFormObject($idForm) {
        $this->currentIdForm = $idForm;
        
        if($this->getStatemenForm()->execute()&&$this->getStatementFieldList()->execute()){
        $arrayFormInfo =$this->getStatemenForm()->fetch();
        
        $arrayFieldsFormInfo =$this->getStatementFieldList()->fetchAll();
        
        $Form= new Form($arrayFormInfo,$arrayFieldsFormInfo);
        return $Form;
        }else{
            throw new \Exception(__CLASS__.' '.__METHOD__.' Error PDOStatement Execute codeErrorPDO= '.$this->getStatemenForm()->errorCode().' and '.$this->getStatementFieldList()->errorCode());
        }
    }
    
    public function getCaptcha($POST) {
        
        return new CaptchaStandart($POST["captcha_sid"], $POST["captcha_word"], $this->DB);
    }
/**
 * 
 * @return \PDOStatement
 */
    private function getStatemenForm() {
        if (!$this->StatementForm) {
            $stmt = $this->DB->prepare('SELECT * FROM `b_form` where ID = :id_form');
            assert($stmt instanceof \PDOStatement);
            $stmt->bindParam(':id_form', $this->currentIdForm);

            $this->StatementForm = $stmt;
        }
        return $this->StatementForm;
    }
/**
 * 
 * @return \PDOStatement
 */
    private function getStatementFieldList() {
        if (!$this->StatementFieldList) {
            $stmt = $this->DB->prepare('SELECT `b_form_field`.*, `b_form_answer`.`MESSAGE`, `b_form_answer`.`FIELD_TYPE`, `b_form_answer`.`FIELD_WIDTH`, `b_form_answer`.`FIELD_HEIGHT`, `b_form_answer`.`FIELD_PARAM` FROM `b_form_field` inner join b_form_answer on `b_form_field`.`ID`= `b_form_answer`.FIELD_ID where FORM_ID=:id_form');
            assert($stmt instanceof \PDOStatement);
            $stmt->bindParam(':id_form', $this->currentIdForm);
            $this->StatementFieldList = $stmt;
        }
        return $this->StatementFieldList;
    }

    //put your code here
}
