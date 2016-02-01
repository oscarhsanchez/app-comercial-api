<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_R_USU_CLI);


class cliente_cond_especiales_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
     ----------------------------------------*/
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT cond_especiales.*
             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = albaranes_cab.fk_cliente AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."' AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."')
             JOIN cond_especiales ON cond_especiales.fk_entidad = clientes.fk_entidad AND (cond_especiales.fk_cliente = clientes.pk_cliente OR cond_especiales.fk_cliente = clientes.fk_cliente_cond_esp ) AND cond_especiales.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR cond_especiales.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";
        //Quitamos albaranes y visitas para meojrar rendimiento 2015-11-13 Jaime
        $q = "SELECT cond_especiales.*
                 FROM clientes
                 LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
                 JOIN cond_especiales ON cond_especiales.fk_entidad = clientes.fk_entidad AND (cond_especiales.fk_cliente = clientes.pk_cliente) AND cond_especiales.estado >= ".$state."
                 WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
                 AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  )
                 AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR cond_especiales.updated_at > '".$lastTimeStamp."' )
                 AND clientes.estado > 0
             UNION
              SELECT cond_especiales.*
                 FROM clientes
                 LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
                 JOIN cond_especiales ON cond_especiales.fk_entidad = clientes.fk_entidad AND (cond_especiales.fk_cliente = clientes.fk_cliente_cond_esp ) AND cond_especiales.estado >= ".$state."
                 WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
                 AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  )
                 AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR cond_especiales.updated_at > '".$lastTimeStamp."' )
                 AND clientes.estado > 0

             ";

        return $q;

    }

    /**
     * Funcion que devuelve las condiciones especiales de los clientes asignados al usuario
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

    /**
     * Devuelve una Cond especial de cliente a partir de su token
     *
     * @param $token
     * @param $entityId
     * @return mixed
     */
    function getCondEspecialByToken($token) {


        $this->db->where('token', $token);
        $query = $this->db->get('cond_especiales');

        $cond_especiale = $query->row(0, 'cond_especiales');

        return $cond_especiale;

    }

    /**
     * Devuelve las Cond especiales de un cliente
     *
     * @param $clientePk
     * @param $entityId
     * @param $state
     *
     * @return mixed
     */
    function getCondsEspecialesByClientePk($entityId, $clientePk, $state) {


        $this->db->where('fk_cliente', $clientePk);
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state);
        $query = $this->db->get('cond_especiales');

        $cond_especiales = $query->result('cond_especiales');

        return array("conds_especiales" => $cond_especiales?$cond_especiales:array());

    }

    /**
     * Funcion que guarda la cond de especial en la bbdd
     *
     * @param $condEspecial
     * @return bool
     * @throws APIexception
     */
    function saveCondEspecial($condEspecial) {
        $this->load->model("log_model");

        if (!isset($condEspecial->token)) {
            $condEspecial->token = getToken();
        }

        $result = $condEspecial->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cliente_cond_especiales_model->saveCondEspecial. Unable to update cond especial.", ERROR_SAVING_DATA, serialize($condEspecial));
        }
    }


	
}

?>