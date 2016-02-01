<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_VISITA);


class cliente_visita_model extends CI_Model {

    /** 
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {
        $q = "SELECT * FROM visita
              WHERE fecha_visita >= DATE_ADD(CURDATE(), INTERVAL -30 DAY) AND fk_entidad = ".$entityId." AND visita.estado >= ".$state." AND updated_at > '".$lastTimeStamp."'
              AND (fk_vendedor = '".$userPk."' OR fk_vendedor_reasignado = '".$userPk."')";

        return $q;
    }

    private function getEntityQuery($entityId, $state, $lastTimeStamp, $fromDate) {
        $q = "SELECT * FROM visita
              WHERE fecha_visita >= '".$fromDate."' AND fk_entidad = ".$entityId." AND visita.estado >= ".$state." AND updated_at > '".$lastTimeStamp."'";

        return $q;
    }

    /**
     * @param $visitaToken
     * @return mixed
     */
    function getVisitaByToken($visitaToken) {
        $this->db->where('token', $visitaToken);
        $query = $this->db->get('visita');

        $visita = $query->row(0, 'visita');
        return $visita;
    }

    /**
     * @param $visita
     * @return bool
     * @throws APIexception
     */
    function saveVisita($visita) {
        $this->load->model("log_model");

        if (!isset($visita->token)) {
            $visita->token = getToken();
        }

        $result = $visita->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cliente_visita_model->saveVisita. Unable to update visita.", ERROR_SAVING_DATA, serialize($visita));
        }
    }

    /**
     * Funcion que devuelve las visitas de un usuario
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de las visitas a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     * * IMPORTANTE: Las visitas se limitan en el pasado a 30 dias.
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array({$visitas})
     *
     */
    function getMultipartCachedVisitaCliente($userPk, $entityId, $pagination, $state=0, $lastTimeStamp=null) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $visitas = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $visitas = $query->result('visita');

            $rowcount = sizeof($visitas);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($visitas, $pagination->pageSize);

                $visitas = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "visitas" => $visitas?$visitas:array());

    }

    /**
     * Funcion que devuelve las visitas de la entidad
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de las visitas a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     * * IMPORTANTE: Las visitas se limitan en el pasado a 30 dias.
     *
     * @param $fromDate
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array({$visitas})
     *
     */
    function getMultipartCachedEntityVisitaCliente($entityId, $pagination, $state=0, $lastTimeStamp=null, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $visitas = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getEntityQuery($entityId, $state, $lastTimeStamp, $fromDate);

            $query = $this->db->query($query);

            $visitas = $query->result('visita');

            $rowcount = sizeof($visitas);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($visitas, $pagination->pageSize);

                $visitas = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "visitas" => $visitas?$visitas:array());

    }
	
}

?>