<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_CONTACTO);


class captacion_contacto_model extends CI_Model {

    /**
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {

        $q = " SELECT DISTINCT cliente_contacto.*
             FROM clientes
             JOIN r_usu_cap ON r_usu_cap.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cap.fk_cliente AND fk_usuario_vendedor = '".$userPk."' AND r_usu_cap.estado > 0
                                AND (CURDATE() BETWEEN r_usu_cap.fecha_desde AND r_usu_cap.fecha_hasta OR r_usu_cap.fecha_hasta IS NULL)
             JOIN cliente_contacto ON cliente_contacto.fk_cliente = clientes.pk_cliente AND cliente_contacto.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 1 AND clientes.fk_entidad = ".$entityId."
             AND (r_usu_cap.updated_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR cliente_contacto.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";

        return $q;
    }


    /**
     * Devuelve el cliente_agrupacion en base a la clave primaria (id)
     *
     * @param $id
     * @return $cliente_agrupacion
     *
     */
    function getRappelByPK($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('cliente_contacto');

        $contact = $query->row(0, 'cliente_contacto');
        return $contact;
    }

    /**
     * Funcion que devuelve los contactos de los clientes de captacion asignados a un usuarios (Vendedor o repartidor)
     * paginados y opcionalmente a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los clientes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({$cliente, $r_usu_client})
     *
     */
    function getMultipartCachedClienteContacto($userPk, $entityId, $pagination, $state=0, $lastTimeStamp=null) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $contacts = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $contacts = $query->result('cliente_contacto');

            $rowcount = sizeof($contacts);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_clients = array_chunk($contacts, $pagination->pageSize);

                $contacts = $chunk_clients[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_clients); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_clients[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "contactos" => $contacts?$contacts:array());

    }

}

?>