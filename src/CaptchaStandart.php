<?php

namespace f00x\AntiBitrix;

/**
 * Description of CapchaValidate
 *
 * @author adm.e.lisin
 */
class CaptchaStandart implements CaptchaInterface {

    /**
     * @var string
     */
    private $idCapcha;
    private $inputString;

    /**
     *
     * @var  \PDO
     */
    private $DB;

    public function __construct($idCapcha, $inputString, \PDO $DB) {
        $this->idCapcha = $this->keyDbEscape($idCapcha);
        $this->inputString = strtoupper($this->keyDbEscape($inputString));
        $this->DB = $DB;
    }

    private function keyDbEscape($string) {
        return preg_replace('%[^0-9A-z]%', '', $string);
    }

    public function isValid() {
        $stmt = $this->getStatementIsValid();
        if ($stmt->execute() && $stmt->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    private function getStatementIsValid() {
        $stmt = $this->DB->prepare('SELECT * FROM `b_captcha` WHERE `ID`=:id_capcha AND `CODE`=:key_capcha');

        $stmt->bindParam(':id_capcha', $this->idCapcha);
        $stmt->bindParam(':key_capcha', $this->inputString);

        return $stmt;
    }

    //put your code here
}
