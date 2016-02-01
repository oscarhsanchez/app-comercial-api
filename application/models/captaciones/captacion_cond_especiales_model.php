<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_R_USU_CLI);


class captacion_cond_especiales_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
    ----------------------------------------*/
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {

        $q = " SELECT DISTINCT cond_especiales.*
             FROM clientes
             JOIN r_usu_cap ON r_usu_cap.fk_entidad = ".$entityId." AND  clientes.pk_cliente = r_usu_cap.fk_cliente AND fk_usuario_vendedor = '".$userPk."' AND r_usu_cap.estado > 0
                                AND (CURDATE() BETWEEN r_usu_cap.fecha_desde AND r_usu_cap.fecha_hasta OR r_usu_cap.fecha_hasta IS NULL)
             JOIN cond_especiales ON cond_especiales.fk_entidad = ".$entityId." AND  cond_especiales.fk_cliente = clientes.pk_cliente AND cond_especiales.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 1 AND clientes.fk_entidad = ".$entityId."
             AND (r_usu_cap.updated_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR cond_especiales.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";

        return $q;

    }

    /**
     * Funcion que devuelve las condiciones especiales de los clientes de captacion asignados al usuario
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los clientes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param int $state
     * @param $lastTimeStamp
     * @return array($pagination, array($cond_especiales))
     *
     */
    function getMultipartCachedSpecialConditions($userPk, $entityId, $pagination, $state=0, $lastTimeStamp=null) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $special_conditions = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $special_conditions = $query->result('cond_especiales');

            $rowcount = sizeof($special_conditions);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($special_conditions, $pagination->pageSize);

                $special_conditions = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "conds_especiales" => $special_conditions?$special_conditions:array());

    }

}

?>