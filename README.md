# anti bitrix library
Эта библиотека изначально создана. По скольку стандартным функционал не делает то, что должен делать.
## Installation

### Composer (recommended)

Use [Composer](https://getcomposer.org) to install this library from Packagist:
[`f00x/anti-bitrix`](https://packagist.org/packages/google/recaptcha)

Run the following command from your project directory to add the dependency:

```sh
composer require f00x/anti-bitrix 
```

Alternatively, add the dependency directly to your `composer.json` file:

```json
"require": {
    "f00x/anti-bitrix":""
}
```
Example use.

```php
use f00x\AntiBitrix;
require_once '\\vendor\\autoload.php';
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
/**
$settingsBitrix = require( dirname($_SERVER["DOCUMENT_ROOT"]) . "/bitrix/.settings.php" );
*/
$settingsBitrix=array(
    /* Other bitrix param bitrix/.settings.php */
    'connections' =>
    array(
        'value' =>
        array(
            'default' =>
            array(
                'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
                'host' => 'hostDB',
                'database' => 'test',
                'login' => 'root',
                'password' => 'password',
                'options' => 2.0,
            ),
        ),
        'readonly' => true,
    ),
    'anti_bitrix' => [
        'smtp' => [
            'host' => 'smtp.example.com',
            'port' => '465',
            'username' => 'from',
            'password' => 'password',
            'encryption' => 'ssl',
            'is_ntlm' => false,
            'from' => 'from@example.com'
        ],
        're_captcha'=>[
            'site_key'=>'key',
            'secred_key'=>'key2',
            
        ]
    ],
);

$ConfigManager = new AntiBitrix\ConfigManager($settingsBitrix);
$Proxy = new AntiBitrix\ProxyForm($ConfigManager);

$FormManager = new AntiBitrix\FormManager($ConfigManager->getMysqlPDOConnect());
// 1 - id form in bitrix admin
$Form = $FormManager->getFormObject('1');
if ($Form instanceof Form) {
$Proxy->addForm($Form);
$Proxy->proxyPostDataForm();
}
```
Learn about the successful completion of the form and CAPTCHA can be so.
```php
// 1 - id form in bitrix admin
if($Proxy->isSuccess(1))
{/* print message and other */
}
```
## Send Email
### basic use case
This package also allows 
-Create letters by template.
-To do dispatch on data and object of dispatch

```php
$SendManager = new SmtpMessageManager($ConfigManager->getMysqlPDOConnect());
$SendManager->setSmtp($ConfigManager->getSmtpMailer());
$SendManager->sendMsgForm($Form)
```
"Template" and "Sending" are taken from the database based on the key (table `b_form` column `MAIL_EVENT_TYPE`)  

Step cofig bitrix form send email
1) create form and fill  "Event IDs:"->"event1:" (table `b_form` column `MAIL_EVENT_TYPE`)
2) create "Email event types" item (set MAIL_EVENT_TYPE value)
3) create "E-Mail templates" item
4) create "Newsletter categories" item
### Custom use
Create custom class by  "MessageManagerInterface"
A basic example in the source code of a class "SmtpMessageManager"






