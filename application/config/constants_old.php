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
define('ACCESS_FORBIDEN', 5000);
define('ERROR_GETTING_INFO', 6000);

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
|
| Definicion de parametros de configuracion
|
*/

/* DEV  */


define('TOKEN_SALT_KEY', './123$eFinanzas!!.!$eSocialRB))(45');
define('SESSION_TIMEOUT', 60); //Minutes
define('FB_APP_ID', '638843436165683');
define('FB_SECRET', 'e9a4c5856fcae0eb396d11e6721f8752');
define('TWITTER_KEY', 'lx9StMgAc6wSAsDLGsA');
define('TWITTER_SECRET', 'q5ffBz9v3rxlzx2a6tMCdGki9GYESxXoSxgU1I6pQ');
define('MULTIPART_CACHE_PAGINATION_MAX_SIZE', 100000);
define('MULTIPART_CACHE_PAGINATION_EXPIRE_TIME', 4000);
define('MULTIPART_AMH_SESSION_EXPIRE_TIME', 600);

define('MEMCACHE_SERVER', '192.162.12.50'); //192.162.12.50

define('SOLR_SERVER', 'localhost:8983/solr');
 
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
| MODELS PATH
|--------------------------------------------------------------------------
|
| Definicion de rutas para las clases del Modelo
|
*/
define('MODEL_USER', '/models/usuario_model.php');
define('MODEL_SESSION', '/models/session_model.php');


/*
|--------------------------------------------------------------------------
| ENTITIES PATH
|--------------------------------------------------------------------------
|
| Definicion de rutas para las entidades
|
*/
define('EF_BASE_CONTROLLER', '/controllers/ef_controller.php');
define('ENTITY_ESOCIAL_ENTITY', '/entities/eEntity.php');
define('ENTITY_APIERROR', '/entities/APIerror.php');
define('ENTITY_USER', '/entities/usuario.php');
define('ENTITY_ENTITY_USER', '/entities/entityUser.php');
define('ENTITY_DEVICE', '/entities/dispositivo.php');
define('ENTITY_ENTITY', '/entities/entity.php');
define('ENTITY_PARAMETER_TPV', '/entities/parameterTpv.php');
define('ENTITY_VERSION_CONTROL', '/entities/versionControl.php');
define('ENTITY_SERIE', '/entities/serie.php');
define('ENTITY_ORDER', '/entities/pedido.php');
define('ENTITY_ORDER_LINE', '/entities/pedidoLine.php');
define('ENTITY_WAYBILL', '/entities/albaran.php');
define('ENTITY_WAYBILL_LINE', '/entities/albaranLine.php');
define('ENTITY_BUDGET', '/entities/presupuesto.php');
define('ENTITY_BUDGET_LINE', '/entities/presuLine.php');
define('ENTITY_INVOICE', '/entities/factura.php');
define('ENTITY_INVOICE_LINE', '/entities/facturaLine.php');
define('ENTITY_REVENUE', '/entities/ingreso.php');
define('ENTITY_REVENUE_LINE', '/entities/ingresoLine.php');
define('ENTITY_EXCEPTION', '/entities/APIexception.php');
define('ENTITY_DELEGACION', '/entities/delegacion.php');
define('ENTITY_CLIENT', '/entities/cliente.php');
define('ENTITY_COND_PAGO', '/entities/cond_pago.php');
define('ENTITY_CLIENTE_AGRUPACION', '/entities/cliente_agrupacion.php');
define('ENTITY_CLIENTE_COND_PAGO', '/entities/cliente_cond_pago.php');
define('ENTITY_CLIENTE_RAPPEL', '/entities/cliente_rappel.php');
define('ENTITY_CLIENTE_CONTACTO', '/entities/cliente_contacto.php');
define('ENTITY_R_USU_CLI', '/entities/r_usu_cli.php');
define('ENTITY_R_USU_CAP', '/entities/r_usu_cap.php');
define('ENTITY_R_CLI_AGR', '/entities/r_cli_agr.php');
define('ENTITY_COND_ESPECIALES', '/entities/cond_especiales.php');
define('ENTITY_PRODUCT', '/entities/articulo.php');
define('ENTITY_MARCA_ARTICULO', '/entities/marcaArticulo.php');
define('ENTITY_PRODUCT_IMG', '/entities/articuloImagen.php');
define('ENTITY_PRODUCT_SUBFAMILY', '/entities/articuloSubFamilia.php');
define('ENTITY_PRODUCT_FAMILY', '/entities/articuloFamilia.php');
define('ENTITY_PRODUCT_GROUP', '/entities/articuloGrupo.php');
define('ENTITY_PRODUCT_AGR', '/entities/articuloAgr.php');
define('ENTITY_R_ART_AGR', '/entities/r_art_agr.php');
define('ENTITY_CASH_COUNT', '/entities/arqueo.php');
define('ENTITY_CASH_COUNT_LINE', '/entities/arqueoLine.php');
define('ENTITY_VISITA', '/entities/visita.php');
define('ENTITY_PAIS', '/entities/pais.php');
define('ENTITY_PROVINCIA', '/entities/provincia.php');
define('ENTITY_ZONA', '/entities/zona.php');
define('ENTITY_SUBZONA', '/entities/subZona.php');
define('ENTITY_TARIFA', '/entities/tarifa.php');
define('ENTITY_TARIFA_ARTICULO', '/entities/tarifa_articulo.php');
define('ENTITY_TARIFA_DELEGACION', '/entities/tarifa_delegacion.php');
define('ENTITY_TARIFA_CLIENTE', '/entities/tarifa_cliente.php');
define('ENTITY_VENTA_DIRIGIDA', '/entities/venta_dirigida.php');
define('ENTITY_MOTIVO_NO_VENTA_VENTA_DIRIGIDA', '/entities/motivoNoVentaVentaDirigida.php');
define('ENTITY_LINEA_MERCADO', '/entities/linea_mercado.php');
define('ENTITY_USUARIO_GEO', '/entities/usuario_geo.php');
define('ENTITY_FORMA_PAGO', '/entities/forma_pago.php');
define('ENTITY_R_ART_ALM', '/entities/r_art_alm.php');
define('ENTITY_R_ART_PRO', '/entities/r_art_pro.php');
define('ENTITY_PROMOCION', '/entities/promocion.php');
define('ENTITY_GRUPO_REGLA', '/entities/grupo_regla.php');
define('ENTITY_REGLA', '/entities/regla.php');
define('ENTITY_REGLA_PARAMETRO', '/entities/regla_parametro.php');
define('ENTITY_REGLA_VALOR', '/entities/regla_valor.php');
define('ENTITY_MOTIVO_NO_VENTA', '/entities/motivoNoVenta.php');
define('ENTITY_ALMACEN', '/entities/almacen.php');
define('ENTITY_RECIBO_COBRO', '/entities/recibo_cobro.php');
define('ENTITY_REFERENCIA_MPV', '/entities/referencia_mpv.php');
define('ENTITY_TIPO_MPV', '/entities/tipo_mpv.php');
define('ENTITY_R_DEL_MPV', '/entities/r_del_mpv.php');
define('ENTITY_INCIDENCIA', '/entities/incidencia.php');
define('ENTITY_REGISTRO_INCIDENCIA', '/entities/registro_incidencia.php');
define('ENTITY_MOVIMIENTO_ALMACEN', '/entities/movimientoAlmacen.php');
define('ENTITY_MOVIMIENTO_ALMACEN_LIN', '/entities/movimientoAlmacenLine.php');
define('ENTITY_INVENTARIO', '/entities/inventario.php');
define('ENTITY_INVENTARIO_LIN', '/entities/inventarioLine.php');
define('ENTITY_MOTIVO_PROMOCION', '/entities/motivoPromocion.php');
define('ENTITY_REPO_CARPETA', '/entities/repoCarpeta.php');
define('ENTITY_REPO_ARCHIVO', '/entities/repoArchivo.php');
define('ENTITY_R_PED_PRO', '/entities/r_ped_pro.php');
define('ENTITY_CLIENTE_AMH', '/entities/cliente_amh.php');
define('ENTITY_PROVEEDOR', '/entities/Proveedor.php');
define('ENTITY_PEDIDO_PROVEEDOR_CAB', '/entities/pedidoProveedorCab.php');
define('ENTITY_PEDIDO_PROVEEDOR_LIN', '/entities/pedidoProveedorLin.php');
define('ENTITY_ARTICULO_FAVORITO', '/entities/articuloFavorito.php');
define('ENTITY_ARTICULO_LOTE', '/entities/articuloLote.php');


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