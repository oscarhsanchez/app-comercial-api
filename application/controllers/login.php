<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php
require(APPPATH.LIBRARY_RESTJSON);
require(APPPATH.ENTITY_USER);
require(APPPATH.ENTITY_APIERROR);

class login extends REST_Controller
{
	function __construct()
    {
    	parent::__construct();
    	$this->load->model('usuarios/session_model');
        $this->load->model('usuarios/user_model');
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
        if((!$this->post('renew_token') || !$this->post('access_token') || !$this->post('deviceid') || !$this->post('countryid')) && (!$this->post('deviceid') || !$this->post('countryid') || !$this->post('username') || !$this->post('password')) ) {
            $err = new APIerror(INVALID_NUMBER_OF_PARAMS);
            $result = $err->getValues();
            $this->response(array('result' => 'error', 'error' => $result), 200);
        }

        $username = $this->post('username');
        $pass = $this->post('password');
        $countryId = $this->post('countryid');
        $deviceId = $this->post('deviceid');

        $renew_token = $this->post('renew_token');
        $access_token = $this->post('access_token');

        if ($username && $pass) {

            $user = $this->user_model->getUserByUserName($username);

            if ($user) {
                $salt = $user->salt;
                $encryptedPass = bCryptPassword($pass, $salt, 13);

                if ($user->password == $encryptedPass) {
                    $new_session = $this->session_model->createSession($user->id, $user->roles, $countryId, $deviceId);
                    $this->user_model->updateLastLogin($user->id);
                    $this->esocialmemcache->set($new_session->access_token, serialize($new_session), false, SESSION_TIMEOUT);
                    $this->response(array('result' => 'OK', 'Session' => $new_session), 200);
                } else {
                    $err = new APIerror(INVALID_USERNAME_OR_PASS);
                    $result = $err->getValues();
                    $this->response(array('result' => 'error', 'error' => $result), 200);
                }
            } else {
                $err = new APIerror(INVALID_USERNAME_OR_PASS);
                $result = $err->getValues();
                $this->response(array('result' => 'error', 'error' => $result), 200);
            }

        } else if ($renew_token && $access_token) {
            $current_session = $this->session_model->getSessionByAccesToken($access_token, $deviceId);
            if ($current_session) {
                $new_session = $this->session_model->renewSession($current_session->fk_user, $current_session->roles, $countryId, $deviceId, $renew_token);
                if ($new_session) {
                    $this->user_model->updateLastLogin($current_session->fk_user);
                    $this->esocialmemcache->set($new_session->access_token, serialize($new_session), false, SESSION_TIMEOUT);
                    $this->response(array('result' => 'OK', 'Session' => $new_session), 200);
                } else {
                    $err = new APIerror(INVALID_TOKEN);
                    $result = $err->getValues();
                    $this->response(array('result' => 'error', 'error' => $result), 200);
                }
            } else {
                $err = new APIerror(INVALID_TOKEN);
                $result = $err->getValues();
                $this->response(array('result' => 'error', 'error' => $result), 200);
            }
        }

    }
}

?>