<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_AGRUPACION);
require_once(APPPATH.ENTITY_R_CLI_AGR);


class cliente_agrupacion_model extends CI_Model {

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
        if ($userPk) {
            //Solo cogemos los clientes activos y las relaciones activas, ya que no me interesan los eliminados. En ese caso se eliminaran automaticamente en el TPV
            $q = " SELECT DISTINCT r_cli_agr.*
                 FROM clientes
                 LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
                 LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
                 LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."' AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."')
                 JOIN r_cli_agr ON r_cli_agr.fk_entidad = ".$entityId." AND r_cli_agr.fk_cliente = clientes.pk_cliente AND r_cli_agr.estado >= ".$state."
                 WHERE clientes.bool_es_captacion = 0 AND clientes.fk_entidad = ".$entityId." AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR albaranes_cab.pk_albaran IS NOT NULL )
                 AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR r_cli_agr.updated_at > '".$lastTimeStamp."' )
                 AND clientes.estado > 0  ";
        } else {
            $q = "SELECT *
                 FROM r_cli_agr
                 WHERE fk_entidad = ".$entityId." AND r_cli_agr.updated_at > '".$lastTimeStamp."'
                 AND estado >= ".$state;
        }

        return $q;
    }

    /**
     * Funcion que guarda la relacion entre el cliente y la agrupacion en la bbdd
     *
     * @param $r_cli_agr
     * @return bool
     * @throws APIexception
     */
    function save($r_cli_agr) {
        $this->load->model("log_model");

        if (!isset($r_cli_agr->token)) {
            $r_cli_agr->token = getToken();
        }

        $result = $r_cli_agr->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cliente_agrupacion_model->saveCondPago. Unable to update cond de pago.", ERROR_SAVING_DATA, serialize($r_cli_agr));
        }
    }


    /**
     * Devuelve el cliente_agrupacion en base a la clave primaria (id)
     * 
     * @param $id
     * @return $cliente_agrupacion
     *
     */
    function getAgrupacionByPK($id) {
		$this->db->where('pk_cliente_agrupacion', $id);
		$query = $this->db->get('cliente_agrupacion');

		$cliente_agrupacion = $query->row(0, 'cliente_agrupacion');
		return $cliente_agrupacion;
	}

    /**
	 * Funcion que devuelve las agrupaciones de los clientes paginadas.
	 *
	 * @param $entityId
	 * @param $limit
	 * @param $offset
	 * @param $state=0
	 * @param $lastTimeStamp=null
	 * @return Array(cliente_agrupacion)
	 *
	 */
    function getMultipartClienteAgrupacion($entityId, $limit, $offset, $state=0, $lastTimeStamp=null) {
        $this->getQuery($entityId, $state, $lastTimeStamp);
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        return count($query->result())?$query->result():array();
    }

    /**
     * Funcion que devuelve las agrupaciones de los clientes.
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     * 
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({cliente_agrupacion})
     *
     */
    function getMultipartCachedClienteAgrupacion($entityId, $pagination, $state=0, $lastTimeStamp=null) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $groups = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->getQuery($entityId, $state, $lastTimeStamp);
            $query = $this->db->get();

            $groups = $query->result('cliente_agrupacion');

            $rowcount = sizeof($groups);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_clients = array_chunk($groups, $pagination->pageSize);

                $groups = $chunk_clients[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_clients); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_clients[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "groups" => $groups?$groups:array());

    }

    /**
     * Funcion que devuelve la relacion entre clientes y agrupaciones.
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     * Devuelve solo las de los clientes asignados al usuario
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @param $userPk
     * @return $pagination<br/> array({r_cli_agr})
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

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function search($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_cliente_agrupacion';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('cliente_agrupacion');
        $this->db->where('fk_entidad', $entityId);

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }


	
}

?>