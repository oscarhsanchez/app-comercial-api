<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_MOTIVO_NO_VENTA);


class motivos_no_venta_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
    ----------------------------------------*/
    private function getQuery($entityId, $state, $lastTimeStamp) {
        $this->db->select('motivos_no_venta.*');
        $this->db->from('motivos_no_venta');
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state, false);
        $this->db->where('updated_at >', $lastTimeStamp);
    }


    /**
     *
     * Funcion que devuelve los motivos de no venta de una entidad
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los registros a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param int $state
     * @param null $lastTimeStamp
     * @return array($pagination, array($motivo_no_venta))
     *
     *
     */
    function getMultipartCachedMotivos($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $motivos = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->getQuery($entityId, $state, $lastTimeStamp);
            $query = $this->db->get();

            $motivos = $query->result('motivoNoVenta');

            $rowcount = sizeof($motivos);

            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($motivos, $pagination->pageSize);

                $motivos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "motivos" => $motivos?$motivos:array());

    }

}

?>