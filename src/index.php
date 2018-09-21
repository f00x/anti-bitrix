<?php
namespace f00x\AntiBitrix;
spl_autoload_register(function ($name) {
    
    $name= str_replace(__NAMESPACE__.'\\', '', $name);
    include  $name.'.php';
    
});
//require 'CaptchaInterface.php';
//require 'CaptchaStandart.php';
//require 'ConfigManager.php';
//require 'Form.php';
//require 'FormField.php';
//require 'FormManager.php';

$settingsBitrix=require( $_SERVER["DOCUMENT_ROOT"] . "/bitrix/.settings.php" );

ini_set ( string $varname , string $newvalue )

$ConfigManager = new ConfigManager($settingsBitrix);

$POST = array ( 'sessid' => 'cb43b2ac2ef7f7ad46f24feed4d5c37c', 
    'WEB_FORM_ID' => '1',
    'lang' => 'ru',
    'form_text_1' => 'fio',
    'form_text_2' => '799999999', 
    'form_text_3' => 'e.lisin@mfc38.ru', 
    'form_textarea_4' => 'test', 
    'captcha_sid' => '06a013c86bc1adafc53bc4daf53f8d3f', 
    'captcha_word' => 'M4232',
    'web_form_submit' => 'Отправить.' );

$FormManager = new FormManager($ConfigManager->getMysqlPDOConnect());
$Form=$FormManager->getFormObject('1');

$Form->handleRequestPOST($POST);

if($Form->isSubmitted()){
echo "<p>Submited</p>";   
}
if($Form->isSubmitted()&&$Form->isValid()){
echo "<p>Valid</p>"; 

$SendManager= new SendEmailManager($ConfigManager->getMysqlPDOConnect());
$SendManager->sendMsgForm($Form);

}if($Form->isCaptcha()){
echo "<p>Captcha Use</p>";   

$Captcha=$FormManager->getCaptcha($POST);
if($Captcha->isValid()){
  echo "<p>Captcha Valid</p>";  
}

}





//$Capcha=new f00x\AntiBitrix\CaptchaStandart("0d186b6beab9d268cedbe726b016f9b9",'H2QHk',$ConfigManager->getMysqlPDOConnect());
//
//  var_dump( $Capcha->isValid());     
//$Capcha2=new f00x\AntiBitrix\CaptchaStandart("0d186b6beab9d268cedbe726b016f9b9",'H2QHk11',$ConfigManager->getMysqlPDOConnect());
// var_dump( $Capcha2->isValid());   