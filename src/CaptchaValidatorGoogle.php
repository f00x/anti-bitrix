<?php

/*
 * Copyright (C) 2018 adm.e.lisin
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace f00x\AntiBitrix;

use ReCaptcha\ReCaptcha;

/**
 * Description of CaptchaGoogle
 *
 * @author adm.e.lisin
 */
class CaptchaValidatorGoogle implements CaptchaValidatorInterface {

    private $secretKey;

    /**
     *
     * @var ReCaptcha
     */
    private $ReCaptcha;
    
    

    public function __construct($secret_key,$responce,\PDO $DB) {
        $this->secretKey = $secret_key;
        $this->respounce= $responce;
        $this->ReCaptcha = new ReCaptcha($secret_key);
    }

    public function isValid() {
        $resp=$this->ReCaptcha->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
        return $resp->isSuccess();
    }

}
