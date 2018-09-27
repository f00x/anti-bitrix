<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace f00x\AntiBitrix;

/**
 *
 * @author adm.e.lisin
 */
interface CaptchaValidatorInterface {
   public function __construct($secret_key, $responce, \PDO $DB);
   public function isValid();
}
