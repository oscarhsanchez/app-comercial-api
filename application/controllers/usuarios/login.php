<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require(APPPATH.LIBRARY_RESTJSON);
require(APPPATH.ENTITY_USER);
require(APPPATH.ENTITY_APIERROR);
require(APPPATH.ENTITY_DEVICE);

class login extends REST_Controller
{
	function __construct()
    {
    	parent::__construct();
    	$this->load->model('usuarios/session_model');
    	$this->load->model('usuarios/usuario_model');
    	$this->load->model('usuarios/dispositivo_model');
        $this->load->model('entidad/entity_model');
        $this->load->model('documentos/serie_model');
        $this->load->model('documentos/presupuesto_model');
        $this->load->model('documentos/ingreso_model');
        $this->load->helper("esocialutils");
    }

    public function serverDateTime_get()
    {
        $this->response(array('result' => 'OK', 'datetime' => $this->session_model->getServerDateTime()), 200);
    }



    /**
     *  Login principal del API
     *
     * @param $deviceId
     * @param $entitySecret
     * @param $mail
     * @param $pass - Sin encriptar
     * @return $usuarios, $token
     *
     */
	public function index_post()
    {    
        
    	// Check for required parameters
        if(!$this->post('deviceId') || !$this->post('entitySecret') || !$this->post('mail') || !$this->post('pass')) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

         
        $entity = $this->entity_model->getEntityBySecretKey($this->post('entitySecret'));
        if ($entity) {
            //Tambien validamos contra el nombre de usuario.
            $entityUser = $this->usuario_model->getEntityUserByMailOrCod($this->post('mail'), $entity->pk_entidad);

            if ($entityUser) {
                $raw = $this->post('pass');
                $salt = $entityUser->salt;

                $encryptedPass = encryptPass($raw, $salt, 5, "sha512", true);


                if ($entityUser->pass == $encryptedPass) {

                    //Verificamos el terminal
                    $device = $this->dispositivo_model->getDeviceByUniqueId($this->post('deviceId'), $entity->pk_entidad);
                    if ($device) {
                        //Creamos la session
                        $token = $this->session_model->createSession($entityUser->id_usuario, $device->id_dispositivo, $entityUser->fk_entidad);

                        $this->response(array('result' => 'OK', 'entityUser' => $entityUser, 'token' => $token), 200);
                    } else {
                        $err = new APIerror(DEVICE_NOT_REGISTERED);
                        $result = $err->getValues();
                        $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
                        $this->response(array('result' => 'error', 'error' => $result), 200);
                    }

                } else {
                    $err = new APIerror(INVALID_USERNAME_OR_PASS);
                    $result = $err->getValues();
                    $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
                    $this->response(array('result' => 'error', 'error' => $result), 200);
                }

            } else {
                $err = new APIerror(INVALID_USERNAME_OR_PASS);
                $result = $err->getValues();
                $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
                $this->response(array('result' => 'error', 'error' => $result), 200);
            }
            
        } else {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }
        
        
    	
    }

    /**
     *  Login mediante clave encriptada
     *
     * @param $deviceId
     * @param $entitySecret
     * @param $mail
     * @param $pass - Encriptado
     * @return $usuarios, $token
     *
     */
    public function encryptedLogin_post()
    {

        // Check for required parameters
        if(!$this->post('deviceId') || !$this->post('entitySecret') || !$this->post('mail') || !$this->post('pass')) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }


        $entity = $this->entity_model->getEntityBySecretKey($this->post('entitySecret'));
        if ($entity) {
            //Encriptamos el pass para compararlo
            $pass =  $this->post('pass');

            $entityUser = $this->usuario_model->getEntityUserByMailPassEntity($this->post('mail'), $pass, $entity->pk_entidad);

            if ($entityUser) {

                //Verificamos el terminal
                $device = $this->dispositivo_model->getDeviceByUniqueId($this->post('deviceId'), $entity->pk_entidad);
                if ($device) {
                    //Creamos la session
                    $token = $this->session_model->createSession($entityUser->id_usuario, $device->id_dispositivo, $entityUser->fk_entidad);

                    $this->response(array('result' => 'OK', 'entityUser' => $entityUser, 'token' => $token), 200);
                } else {
                    $err = new APIerror(DEVICE_NOT_REGISTERED);
                    $result = $err->getValues();
                    $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
                    $this->response(array('result' => 'error', 'error' => $result), 200);
                }



            } else {
                $err = new APIerror(INVALID_USERNAME_OR_PASS);
                $result = $err->getValues();
                $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
                $this->response(array('result' => 'error', 'error' => $result), 200);
            }

        } else {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParams($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), $this->post('deviceId') . '-' . $this->post('entitySecret'). '-' . $this->post('mail') . '-' . $this->post('pass'));
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }



    }

    public function validateSession_get()
    { 
        $userId = $this->input->get_request_header('Userid', TRUE);
        $deviceId =  $this->input->get_request_header('Deviceid', TRUE);
        $rec_token = $this->input->get_request_header('Token', TRUE);
        $entitySecret = $this->input->get_request_header('Entitysecret', TRUE);

        $entity = $this->entity_model->getEntityBySecretKey($entitySecret);

        if (!$entity) {
            $err = new APIerror(ENTITY_NOT_FOUND);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $userId, $entitySecret, $deviceId, $rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

        if (!$rec_token || !$entitySecret || !$userId || !$deviceId) {

            $err = new APIerror(INVALID_NUMBER_OF_HEADER_PARAMS);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $userId, $entitySecret, $deviceId, $rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);

        }

        $token = $this->session_model->validateSession($userId, $deviceId, $entity->pk_entidad, $rec_token);        
        if (!$token) {                       
            $err = new APIerror(INVALID_TOKEN);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $userId, $entitySecret, $deviceId, $rec_token);
            $this->response(array('result' => "error", 'error' => $result));
        } else {
            $this->response(array('result' => "OK"), 200);
        }

    }

    

    

    public function test_get() {



        $this->load->library('JasperReports');
        $entidad = $this->entity_model->getEntityById(3);

        $documento = '2015/1112_2015_56';
        $params = array("fk_entidad" => "56", "pk_pedido" => "2015/1112_2015_56");
        $mails = "jaime.banus@rbconsulting.es, jaime.banus@rbconsulting.es";

        $result = $this->jasperreports->sendReportMailAction($entidad, $documento, 'pedido', $params, $mails);

        print_r($result);


        return;


        /*
        print_r($this->ingreso_model->getReveneuById(6));

        return;

        $options = new stdClass();
        $options->pagination = new stdClass();
        $options->pagination->progresive = new stdClass();
        $options->pagination->progresive->lastId = 234;
        $options->pagination->progresive->limit = 10;
        

        $filters = array();
        
        $filter = new stdClass();
        $filter->field = "id_entidad";
        $filter->type = ">";
        $filter->value = "5";
        $filters[] = $filter;

        $filters = array();
        $filter = new stdClass();
        $filter->field = "cod_presu";
        $filter->type = "=";
        $filter->value = "S2014/45";
        $filters[] = $filter;


        $filter = new stdClass();
        $filter->field = "id_presu_cab";
        $filter->type = "between";
        $filter->value = "5";
        $filter->value2 = "10";
        //$filters[] = $filter;
        $options->filters = $filters;

        echo json_encode($options); */
    

        //PRUEBAS DE INSERT Y UPDATE BUDGETS
        //----------------------------------------------------
        $budget = new presupuesto();
        //$budget->pk_presu_cab = 'S2014/127_11';
        $budget->cod_presupuesto = "S2014/127";
        $budget->fk_entidad = 2;
        $budget->serie = 'S2014';
        $budget->anio = 2014;        
        $budget->num_serie = 127;       
        $budget->fecha = '2014-04-24';
        $budget->raz_social = 'Pruebakkk';
        $budget->nif = '78972400Z';
        $budget->direccion = 'direccion';
        $budget->poblacion = 'poblacion';
        $budget->provincia = 'Provincia';
        $budget->codpostal = '28046';
        $budget->base_imponible_tot = 100;
        $budget->imp_desc_tot = 0;
        $budget->imp_iva_tot = 10;
        $budget->imp_re_tot = 0;
        $budget->imp_total = 110;
        $budget->observaciones = 'observaciones';
        $budget->estado = 1;        
        $budget->token = getToken();
        //$budget->cod_forma_pago = 1;
        //$budget->cod_condicion_pago = "CON";
        $budget->bool_actualiza_numeracion = 1;
        $budget->bool_recalcular = 1;
        $budget->varios10 = "jjj";
        
        $lines = array();
        $budgetLine = new presuLine();
        $budgetLine->concepto = "Prubea";
        $budgetLine->cantidad = 2;
        $budgetLine->precio = 12;
        $budgetLine->base_imponible = 24;
        $budgetLine->descuento = 0;
        $budgetLine->imp_descuento = 0;
        $budgetLine->iva = 10;
        $budgetLine->imp_iva = 2.2;
        $budgetLine->re= 0;
        $budgetLine->imp_re = 0;
        $budgetLine->total_lin = 27;
        $budgetLine->estado = 1;
        $budgetLine->token = sha1(rand().date('Y-m-d H:i:s').rand());
        $budgetLine->cod_concepto = '280333';
        //$budgetLine->token = '8e15056f201584f6a7be2575fb786f6062c0db68';
        $budgetLine->created_at = '';
        $budgetLine->updated_at = '';

        $lines[] = $budgetLine;

        $budgetLine = new presuLine();
        $budgetLine->concepto = "Prubea 22";
        $budgetLine->cantidad = 2;
        $budgetLine->precio = 12;
        $budgetLine->base_imponible = 24;
        $budgetLine->descuento = 0;
        $budgetLine->imp_descuento = 0;
        $budgetLine->iva = 10;
        $budgetLine->imp_iva = 2.2;
        $budgetLine->re= 0;
        $budgetLine->imp_re = 0;
        $budgetLine->total_lin = 26.2;
        $budgetLine->estado = 1;
        $budgetLine->token = sha1(rand().date('Y-m-d H:i:s').rand());
        $budgetLine->cod_concepto = '280033';
        //$budgetLine->token = '8e15056f201584f6a7be2575fb786f6062c0d999';
        $budgetLine->created_at = '';
        $budgetLine->updated_at = '';


        $lines[] = $budgetLine;
        $budget->budgetLines = $lines;

     
       $this->load->library('esocialmemcache');
     
        $tmp       = serialize($budget);
     
        $this->esocialmemcache->add("prueba", $tmp, false, 30);
     
        //echo "Data from the cache:".$this->esocialmemcache->get("prueba");
        print_r(unserialize($this->esocialmemcache->get("prueba")));


       return;
      


        //PRUEBAS DE QUERY BUDGETS
        //----------------------------------------------
      /*  $filters = array();
        $filter = new stdClass();
        $filter->field = "id_entidad";
        $filter->type = ">";
        $filter->value = "5";
        $filters[] = $filter;

        $filters = array();
        $filter = new stdClass();
        $filter->field = "cod_presu";
        $filter->type = "=";
        $filter->value = "S2014/45";
        $filters[] = $filter;


        $filter = new stdClass();
        $filter->field = "id_presu_cab";
        $filter->type = "between";
        $filter->value = "5";
        $filter->value2 = "10";
        //$filters[] = $filter;
            

        $pagination = new stdClass();

        $pagination->progresive = new stdClass();
        $pagination->progresive->lastId = 270;
        $pagination->progresive->limit = 10;
        
        /*$pagination->multipart = new stdClass();
        $pagination->multipart->cache = 1;
        $pagination->multipart->cache_token = "MjAzNDgtMTM5ODMyNzUwNC0yMDQ4NQ==";
        $pagination->multipart->page = 7;
        $pagination->multipart->pageSize = 50;
*/
    /*    echo json_encode($pagination);
        echo '<br><br>';
        echo json_encode($filters);
        echo '<br><br>';


        print_r($this->presupuesto_model->getFilteredBudgets($filters, $pagination, null));
        echo '<br><br>';
        print_r($this->presupuesto_model->getFilteredBudgetsFromIndex($filters, $pagination, null));

        return;

        print_r($this->presupuesto_model->getBudgetByIdFromIndex(3));
        echo '<br><br>';
        print_r($this->presupuesto_model->getBudgetById(3));
        echo '<br><br>';
        print_r($this->presupuesto_model->getBudgetByTokenFromIndex('lllllllasdasldlalsdasd'));


      

        echo '<br><br>';
        $test = json_decode('{"prueba": "jaime", "otro": 2}');
        print_r($test);
        echo $test->prueba;

        $this->session_model->updateSession('738a6d6fb4852cede911cd4ffdad82a1768ce359');
        
        $entity = $this->entity_model->getEntityBySecretKey('f92810dc9a6aae73326161263b6b667288445697');

        $entity->codpostal = null;
        //$entity->id_entidad = null;

        $entity->_save(false);

        ////////////////////////////
        
        //print_r($this->usuario_model->getUserById(2));
        //print_r($this->usuario_model->getEntityUserById(2));
        //
        print_r($this->dispositivo_model->getParameterTpv(1));

        echo '<br><br>';

         print_r($this->dispositivo_model->getLastVersionOfApp(1));

       // echo encryptPass("gabiola", "fd77285587a94a17c704405c87f511b5", 5, "sha512", true);
       // echo '<br><br>';
       // echo encryptPass("gabiola", '', 5, "sha512", true); //No se esta usando el salt. ESTE ES EL BUENO
       // 
       $this->serie_model->setDefaultSerie(11, '2014-', 2014);
       $this->serie_model->updateOrderNum(11, '2014-', 2014, 24);

       echo '<br><br>';

       $budget = $this->presupuesto_model->getBudgetById(4); */

       ///MEMCACHE
        //-----------------------------------------------
 
        $this->load->library('esocialmemcache');
     
        $tmp       = serialize($budget);
     
        $this->esocialmemcache->add("key", $tmp, false, 30);
     
        echo "Data from the cache:<br />";
        print_r(unserialize($this->esocialmemcache->get("key")));

         /*
        //Prueba SOLR
        $this->load->library('esocialsolr');


       

        //AÑADIR DOCUMENTO
        //-----------------------------------------------
        
        $uniqueId = new stdClass();
        $uniqueId->id = "Maria";

        $fields = new stdClass();
        $fields->name = "Maria Lopez";
        $fields->tel = array("2222", "99999");

        $doc = new stdClass();
        $doc->uniqueId = $uniqueId;
        $doc->fields = $fields;

        $uniqueId = new stdClass();
        $uniqueId->id = "Maria2";

        $fields = new stdClass();
        $fields->name = "Maria Lopez2";
        $fields->tel = array("55555", "99999");

        $doc2 = new stdClass();
        $doc2->uniqueId = $uniqueId;
        $doc2->fields = $fields;


        $docs = array($doc, $doc2);

        echo "result" . $this->esocialsolr->insertDocs("collection1", $docs, true);

        //ELIMINAR DOCUMENTO
        //-------------------------------------------------------

        $uniqueId = new stdClass();
        $uniqueId->id = "Maria";

        $uniqueId2 = new stdClass();
        $uniqueId2->id = "Maria2";

        $docs = array($uniqueId, $uniqueId2);

        //echo "result" . $this->esocialsolr->deleteDocs("collection1", $docs, true);

        //ACTUALIZAR DOCUMENTOS
        //-----------------------------------------------------------

        $uniqueId = new stdClass();
        $uniqueId->id = "as4";

        $fields = new stdClass();
        $fields->name = "Pedro Maria";        

        $doc = new stdClass();
        $doc->uniqueId = $uniqueId;
        $doc->fields = $fields;

        $docs = array($doc);

        echo "result" . $this->esocialsolr->updateDocsFields("collection1", $docs, true);

        //INSERTAR DOCUMENTO CON CAMPO GENERICO
        //---------------------------------------------------------------

        $uniqueId = new stdClass();
        $uniqueId->id = "prueba";

        $fields = new stdClass();
        $fields->name = "Prueba Campo Gen";
        $fields->tel = array("55555", "99999");
        $fields->num_i = 5;

        $doc = new stdClass();
        $doc->uniqueId = $uniqueId;
        $doc->fields = $fields;


        $docs = array($doc);

        echo "result" . $this->esocialsolr->insertDocs("collection1", $docs, true);

        //INCREMENTAR CAMPO ENTERO
        //---------------------------------------------------------------------
        
        $fields = new stdClass();
        $fields->num_i = 2; //Incrementamos en 3

        $doc = new stdClass();
        $doc->uniqueId = $uniqueId;
        $doc->fields = $fields;

        $docs = array($doc);

        echo "result" . $this->esocialsolr->incrementField("collection1", $docs, true);

        //ELIMINAR CAMPO
        //------------------------------------------------------------------------

        $fields = array("num_i");

        $doc = new stdClass();
        $doc->uniqueId = $uniqueId;
        $doc->fields = $fields;

        $docs = array($doc);

        echo "result" . $this->esocialsolr->deleteField("collection1", $docs, true);

        //AÑADIR ELEMENTO A UN CAMPO MULTIVALUE
        //-----------------------------------------------------------------------
        //
        $fields = new stdClass();
        $fields->tel = "99988899";

        $doc = new stdClass();
        $doc->uniqueId = $uniqueId;
        $doc->fields = $fields;

        $docs = array($doc);

        echo "result" . $this->esocialsolr->addToMultiValue("collection1", $docs, true);

        echo "<br><br><br>";

        //QUERY SOLR
        //------------------------------------
        
        $this->load->library('solr');
        
        $this->solr->select()
        //->from('collection1')
        ->where('_version_ !=', 0)
        ->order_by('name', 'ASC')
        ->limit(10);
         
        $arrSolr = $this->solr->qAssoc();
        print_r($arrSolr);*/

    }
}

?>