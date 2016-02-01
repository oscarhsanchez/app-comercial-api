<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_COND_PAGO);


class cond_pago_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
     ----------------------------------------*/
    private function getQuery($entityId, $state, $lastTimeStamp) {
        $this->db->select('cond_pago.*');
        $this->db->from('cond_pago');
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state, false);
        $this->db->where('updated_at >', $lastTimeStamp);
    }


    /**
     *
     * Funcion que devuelve las condiciones de pago de una entidad
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los clientes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param int $state
     * @param null $lastTimeStamp
     * @return array($pagination, array($cond_pago))
     *
     *
     */
    function getMultipartCachedCondsPago($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $cond_pagos = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->getQuery($entityId, $state, $lastTimeStamp);
            $query = $this->db->get();

            $cond_pagos = $query->result('cond_pago');

            $rowcount = sizeof($cond_pagos);

            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($cond_pagos, $pagination->pageSize);

                $cond_pagos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "conds_pago" => $cond_pagos?$cond_pagos:array());

    }

    /**
     * Funcion que guarda la cond de pago en la bbdd
     *
     * @param $condPago
     * @return bool
     * @throws APIexception
     */
    function saveCondPago($condPago) {
        $this->load->model("log_model");

        if (!isset($condPago->token)) {
            $condPago->token = getToken();
        }

        $result = $condPago->_save(false, false);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cond_pago_model->saveCondPago. Unable to update cond de pago.", ERROR_SAVING_DATA, serialize($condPago));
        }
    }

    /**
     *
     * @param $token
     * @return the condPago
     */
    function getCondPagoByToken($token) {
        $this->db->where('token', $token);
        $query = $this->db->get('cond_pago');

        $condPago = $query->row(0, 'cond_pago');
        return $condPago;
    }
	
}

?>