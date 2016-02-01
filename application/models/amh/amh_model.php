<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENTE_AMH);


class amh_model extends CI_Model {

    /**
     * Genera una nueva session y devuelve un nuevo token de sesion para amh
     */
    function getTokenSession() {

        $this->load->library('esocialmemcache');

        $token = getToken();

        $clientes = array();

        //Creamos la session en Memcache
        $this->esocialmemcache->add($token, serialize($clientes), false, MULTIPART_AMH_SESSION_EXPIRE_TIME);

        return $token;
    }

    /**
     * Actualiza las session para que no caduque
     *
     * @param $token
     */
    function updateSession($token) {

        $this->load->library('esocialmemcache');

        $clientes = $this->getClientesFromSession($token);

        $this->esocialmemcache->set($token, serialize($clientes), false, MULTIPART_AMH_SESSION_EXPIRE_TIME);

        return $token;
    }

    /**
     * Valida si el token de sesion es valido o no
     *
     * @return true || false
     */
    function validateSession($token) {
        $this->load->library('esocialmemcache');
        $sesion = $this->esocialmemcache->get($token);

        $boolSesion = false;
        if ($sesion) $boolSesion = true;

        return $boolSesion;
    }

    /**
     * Devuelve los clientes de una session
     *
     * @param $token
     * @return Array $cliente_amh
     */
    function getClientesFromSession($token) {
        $this->load->library('esocialmemcache');
        return unserialize($this->esocialmemcache->get($token));
    }

    /**
     * Inserta un array de clientes en la sesion.
     *
     * @param $token
     * @param $clientes
     *
     * return Array $cliente_amh
     */
    function setClientes($token, $clientes) {
        $currentClientes = $this->getClientesFromSession($token);

        foreach ($clientes as $cliente) {
            if (!array_key_exists($cliente->pk_cliente,$currentClientes)) {
                $currentClientes[$cliente->pk_cliente] = $cliente;
            }
        }

        $this->esocialmemcache->set($token, serialize($currentClientes), false, MULTIPART_AMH_SESSION_EXPIRE_TIME);

        return $currentClientes;
    }

    /**
     * Elimina un array de clientes en la sesion.
     *
     * @param $token
     * @param $clientes
     *
     * return Array $cliente_amh
     */
    function delClientes($token, $clientes) {
        $currentClientes = $this->getClientesFromSession($token);

        foreach ($clientes as $cliente) {
            if (array_key_exists($cliente->pk_cliente,$currentClientes)) {
                unset($currentClientes[$cliente->pk_cliente]);
            }
        }

        $this->esocialmemcache->set($token, serialize($currentClientes), false, MULTIPART_AMH_SESSION_EXPIRE_TIME);

        return $currentClientes;
    }





}