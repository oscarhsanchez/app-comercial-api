<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_RAPPEL);


class cliente_rappel_model extends CI_Model {

    /** 
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT cliente_rappel.*
             FROM clientes
              LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."' AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."')
             JOIN cliente_rappel ON cliente_rappel.fk_cliente = clientes.pk_cliente AND cliente_rappel.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR cliente_rappel.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";

        return $q;
    }


    /**
     * Devuelve el rapel en base a la clave primaria (id)
     * 
     * @param $id
     * @return $cliente_agrupacion
     *
     */
    function getRappelByPK($id) {
		$this->db->where('id', $id);
		$query = $this->db->get('cliente_rappel');

		$cliente_cond_pago = $query->row(0, 'cliente_rappel');
		return $cliente_cond_pago;
	}

    /**
     * Devuelve los Rapeles de un cliente
     *
     * @param $clientePk
     * @return Array(cliente_rapel)
     *
     */
    function getByCliente($clientePk, $entityId, $state, $lastUpdate) {
        $this->db->where('fk_cliente', $clientePk);
        $this->db->where('estado >=', $state);
        $this->db->where('updated_at >=', $lastUpdate);
        $query = $this->db->get('cliente_rappel');

        $result = $query->result('cliente_rappel');

        return array("rapeles" => $result?$result:array());;
    }

    /**
     * Funcion que devuelve los rapeles de los clientes asignados a un usuarios (Vendedor o repartidor)
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los clientes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     * 
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({$rapeles})
     *
     */
    function getMultipartCachedClienteRappel($userPk, $entityId, $pagination, $state=0, $lastTimeStamp=null) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $rapeles = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $rapeles = $query->result('cliente_rappel');

            $rowcount = sizeof($rapeles);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($rapeles, $pagination->pageSize);

                $rapeles = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "rapeles" => $rapeles?$rapeles:array());

    }

    /**
     * Devuelve un Rapel a partir de su token
     *
     * @param $token
     * @param $entityId
     * @return mixed
     */
    function getRapelByToken($token) {


        $this->db->where('token', $token);
        $query = $this->db->get('cliente_rappel');

        $rapel = $query->row(0, 'cliente_rappel');

        return $rapel;

    }

    /**
     * Funcion que guardar el rapel en la bbdd
     *
     * @param $rapel
     * @return bool
     * @throws APIexception
     */
    function saveRapel($rapel) {
        $this->load->model("log_model");

        if (!isset($rapel->token)) {
            $rapel->token = getToken();
        }

        $result = $rapel->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cliente_rappel_model->saveRapel. Unable to update rapel.", ERROR_SAVING_DATA, serialize($rapel));
        }
    }
	
}

?>