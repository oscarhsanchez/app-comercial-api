<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| usuarios, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| API Errors
|--------------------------------------------------------------------------
|
| Definicion de los diferente codigos de error
|
*/
define('INVALID_NUMBER_OF_PARAMS', 1000);
define('INVALID_NUMBER_OF_HEADER_PARAMS', 1001);
define('ERROR_SAVING_DATA', 2000);
define('INVALID_PROPERTY_NAME', 2001);
define('SORT_NOT_PERMITED', 2002);
define('MULTISEARCH_NOT_PERMITED', 2003);
define('INVALID_TOKEN', 3000);
define('PARAM_VERIFICATION_ERROR', 4000);
define('FBID_VERIFICATION_ERROR', 4001);
define('USER_NOT_REGISTERED', 4002);
define('INVALID_USERNAME_OR_PASS', 4003);
define('MISSING_USER_MAIL', 4004);
define('MISSING_USER_USERNAME', 4005);
define('USER_WIKKING_ID_ALREADY_USED', 4006);
define('MISSING_USER_MAIL_AND_USER_USERNAME_ALREADY_USED', 4007);
define('MISSING_USER_MAIL_AND_MISSING_USER_USERNAME', 4008);
define('USER_MAIL_ALREADY_USED', 4009);
define('TWITTER_VERIFICATION_ERROR', 4010);
define('DEVICE_ALREADY_EXIST_FOR_PHONE', 4011);
define('CODE_VERIFICATION_ERROR', 4012);
define('DEVICE_NOT_REGISTERED', 4013);
define('DEVICE_NOT_ACTIVATED', 4014);
define('ENTITY_NOT_FOUND', 4015);
define('ENTITY_VERIFICATION_ERROR', 4016);
define('DELEGACION_NOT_FOUND', 4017);
define('NO_LINES_DEFINED', 4018);
define('ERROR_SENDING_MAIL', 4019);
define('ACCESS_FORBIDEN', 5000);
define('ERROR_GETTING_INFO', 6000);
define('ERROR_GETTING_INFO_FROM_MEMCACHE', 6001);
define('ERROR_MAX_PAGINATION_SIZE', 6002);


/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
|
| Definicion de parametros de configuracion
|
*/

/* DEV  */


define('TOKEN_SALT_KEY', './123$vallas!!.!$eSocialRB))(45');
define('RENEW_SALT_KEY', './gPOvall@s!!.!$eSocial))(MXmx');
define('SESSION_TIMEOUT', 600);
define('FB_APP_ID', '638843436165683');
define('FB_SECRET', 'e9a4c5856fcae0eb396d11e6721f8752');
define('TWITTER_KEY', 'lx9StMgAc6wSAsDLGsA');
define('TWITTER_SECRET', 'q5ffBz9v3rxlzx2a6tMCdGki9GYESxXoSxgU1I6pQ');
define('MULTIPART_CACHE_PAGINATION_MAX_SIZE', 100000);
define('MULTIPART_CACHE_PAGINATION_EXPIRE_TIME', 4000);
define('MULTIPART_AMH_SESSION_EXPIRE_TIME', 600);

//Envio SMS
define('SMS_ACCOUNT', 'jaime.banus@rbconsulting.es');
define('SMS_PASS', 'gabiola');

//Envio de mails
define('MAIL_FROM_MAIL', 'jaime.banus@rbconsulting.es');
define('MAIL_FROM_NAME', 'Wouzee');
define('MAIL_PROTOCOL', 'smtp');
define('MAIL_SMTP_HOST', 'smtp.1and1.es');
define('MAIL_SMTP_USER', 'jaime.banus@rbconsulting.es');
define('MAIL_SMTP_PASS', 'Gabiola2009');
define('MAIL_SMTP_PORT', 25);
define('MAIL_TYPE', 'html');

//URL base para amazon
define('AMAZON_BASE_URL', 'https://s3.amazonaws.com/efinanzas/');


/*
|--------------------------------------------------------------------------
| ENTITIES PATH
|--------------------------------------------------------------------------
|
| Definicion de rutas para las entidades
|
*/
define('MAX_PASSWORD_LENGTH', 72);
define('ENTITY_APIERROR', '/entities/APIerror.php');
define('ENTITY_APIEXCEPTION', '/entities/APIexception.php');
define('ENTITY_ESOCIAL_ENTITY', '/entities/eEntity.php');
define('VALLAS_BASE_CONTROLLER', '/controllers/generic_controller.php');
define('ENTITY_SESSION', '/entities/Session.php');
define('ENTITY_USER', '/entities/User.php');
define('ENTITY_CLIENTE', '/entities/Cliente.php');
define('ENTITY_UBICACION', '/entities/Ubicacion.php');
define('ENTITY_MEDIO', '/entities/Medio.php');
define('ENTITY_METADATA_CATEGORY_FQ', '/entities/CategoryFourSquare.php');
define('ENTITY_METADATA_VENUE_FQ', '/entities/VenueFourSquare.php');
define('ENTITY_METADATA_CATORCENA', '/entities/Catorcena.php');
define('ENTITY_METADATA_GASTO', '/entities/Gasto.php');
define('ENTITY_METADATA_PLAZA', '/entities/Plaza.php');
define('ENTITY_PARAMETRO_TPV', '/entities/ParametroTpv.php');
define('ENTITY_AGENCIA', '/entities/Agencia.php');
define('ENTITY_BRIEF', '/entities/Brief.php');


define('GENERIC_MODEL', '/models/generic_model.php');


/*
|--------------------------------------------------------------------------
| LIBRARIES PATH
|--------------------------------------------------------------------------
|
| Definicion de rutas para las librerias
|
*/
define('LIBRARY_RESTJSON', '/libraries/REST_Controller.php');





/* End of file constants.php */
/* Location: ./application/config/constants.php */