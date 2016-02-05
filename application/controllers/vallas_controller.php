<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require_once(APPPATH.LIBRARY_RESTJSON);
//require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.ENTITY_APIERROR);
require_once(APPPATH.ENTITY_SESSION);

class ef_controller extends REST_Controller
{


    public $access_token;
    public $session;

	protected function checkSecurity($action) {
        //Obtenemos Parametros de Cabecera
        $this->access_token = $this->input->get_request_header('Authorization', TRUE);

        $params = array(get_called_class(), $action);
        $this->load->library('Reader', $params);

        $security = $this->reader->getParameter("RequiresPermission");

        if ($security) {

            if (!$this->access_token) {

                $err = new APIerror(INVALID_NUMBER_OF_HEADER_PARAMS);
                $result = $err->getValues();
                $this->response(array('error' => $result), 401);

            }

            //Obtenemos el la session de Memcache
            $session = $this->esocialmemcache->get($this->access_token);

            if ($session) {
                $this->session = unserialize($session);

                //Validamos los permisos
                if ($this->security_model->hasPermission(explode(",", $security[1]), $security[0], $this->session->fk_user, $this->session->getRoles()))    {
                    //Actualizamos la session
                    $this->esocialmemcache->set($this->access_token, serialize($this->session), false, SESSION_TIMEOUT);

                } else {
                    $err = new APIerror(ACCESS_FORBIDEN);
                    $result = $err->getValues();
                    $this->response(array('error' => $result), 401);
                }

            } else {
                $err = new APIerror(INVALID_TOKEN);
                $result = $err->getValues();
                $this->response(array('error' => $result));
            }

        }



    }

    protected function validateParams($received, $parametros) {
    foreach ($parametros as $param) {
        if (!isset($received[$param])) {
            return false;
        }
    }
    return true;
}

}