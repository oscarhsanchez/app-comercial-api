<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_AGRUPACION);
require_once(APPPATH.ENTITY_R_CLI_AGR);


class captacion_agrupacion_model extends CI_Model {

    /** 
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getQuery($entityId, $state, $lastTimeStamp) {
        $this->db->select('cliente_agrupacion.*');
        $this->db->from('cliente_agrupacion');
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('cliente_agrupacion.estado >=', $state);
        $this->db->where('cliente_agrupacion.updated_at >', $lastTimeStamp);
    }

    private function getAssignedRAgrCliQuery($entityId, $userPk, $state, $lastTimeStamp) {
        //Solo cogemos los clientes activos y las relaciones activas, ya que no me interesan los eliminados. En ese caso se eliminaran automaticamente en el TPV
        $q = " SELECT DISTINCT r_cli_agr.*
             FROM clientes
             JOIN r_usu_cap ON r_usu_cap.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cap.fk_cliente AND fk_usuario_vendedor = '".$userPk."' AND r_usu_cap.estado > 0
                                AND (CURDATE() BETWEEN r_usu_cap.fecha_desde AND r_usu_cap.fecha_hasta OR r_usu_cap.fecha_hasta IS NULL)
             JOIN r_cli_agr ON r_cli_agr.fk_entidad = ".$entityId." AND r_cli_agr.fk_cliente = clientes.pk_cliente AND r_cli_agr.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 1 AND clientes.fk_entidad = ".$entityId."
             AND (r_usu_cap.updated_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR r_cli_agr.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";

        return $q;
    }


    /**
     * Funcion que devuelve la relacion entre clientes_captacion y agrupaciones.
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     * Devuelve solo las de los clientes asignados al usuario
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @param $userPk
     * @return $pagination<br/> array({r_cli_cap})
     *
     */
    function getMultipartCachedRCliAgr($entityId, $userPk, $pagination, $state=0, $lastTimeStamp=null) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $result = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedRAgrCliQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $result = $query->result('r_cli_agr');

            $rowcount = sizeof($result);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;


            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($result, $pagination->pageSize);

                $result = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "r_cli_agr" => $result?$result:array());

    }
	
}

?>