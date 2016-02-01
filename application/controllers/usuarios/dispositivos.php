<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.EF_BASE_CONTROLLER);
require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_DEVICE);


class dispositivos extends ef_controller
{
    public $controller = "eFinanzas-EntidadBundle-Controller-DefaultController";
    
	function __construct()
    {
    	parent::__construct();
    	$this->load->model('usuarios/session_model');
        $this->load->model('security_model');
    	$this->load->model('usuarios/usuario_model');
    	$this->load->model('usuarios/dispositivo_model');
        $this->load->model('entidad/entity_model');
        $this->load->helper("esocialutils");
    }



    public function byUniqueId_get()
    {    
    
        $action = "editTerminalesTPV"; 
        
        $this->checkSecurity($action);       

       
        //obtnemos el dispositivo
        $device = $this->dispositivo_model->getDeviceByUniqueId($this->deviceId, $this->entity->pk_entidad);
        if ($device) {
            //Obtenemos el usuarios asociado
            $user = $this->usuario_model->getEntityUserByDevice($device->pk_terminal_tpv);

            if ($user) {
                $this->response(array('result' => 'OK', 'device' => $device, 'user' => $user), 200);
            } else {
                $err = new APIerror(ERROR_GETTING_INFO);
                $result = $err->getValues();
                $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
                $this->response(array('result' => 'error', 'error' => $result), 200);
            }
           
        } else {                
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }    

    }

    public function parameters_get()
    {    
    
        $action = "editTerminalesTPV"; 
        
        $this->checkSecurity($action);       

       
        //obtnemos los parametros
        $parameters = $this->dispositivo_model->getParameterTpv($this->entity->pk_entidad);
        if ($parameters) {
           
            $this->response(array('result' => 'OK', 'parameters' => $parameters), 200);           
           
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }       

    }

    public function lastAppVersion_get()
    {    
    
        $action = "editTerminalesTPV"; 
        $this->checkSecurity($action);  
       
        //obtnemos la ultima version
        $version = $this->dispositivo_model->getLastVersionOfApp($this->entity->pk_entidad);
        if ($version) {           
            $this->response(array('result' => 'OK', 'version' => $version), 200);                      
        } else {
            $err = new APIerror(ERROR_GETTING_INFO);
            $result = $err->getValues();
            $this->log_model->logParamsWithSession($err->getCode(), get_class($this) . '-->' . __FUNCTION__ . ': ' . $err->getDescription(), "GET-->" . serialize($this->get()) . '| POST-->' . serialize($this->post()), $this->userId, $this->entitySecret, $this->deviceId, $this->rec_token);
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }       

    }

    /**
     * Funcion que realiza una busqueda en base a los parametros indicados
     *
     * @param id_entidad
     * @param field_id
     * @param query
     * @param return=null
     * @param type
     * returns Array((value, description))
     */
    public function search_get() {
        $entityId = $this->get('id_entidad');
        $query = $this->get('query');
        $idUser = $this->get('id_user');
        $field = $this->get('field_id');
        $return = $this->get('return');
        $type = $this->get('type');

        $result = $this->dispositivo_model->search($entityId, $field, $query, $return, $type);
        //echo json_encode($result);
        $this->response($result);
    }

}

?>