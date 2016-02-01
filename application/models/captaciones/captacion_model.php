<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_R_USU_CAP);


class captacion_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
    ----------------------------------------*/
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $onlyActivos) {

        $q = " SELECT DISTINCT clientes.pk_cliente, clientes.fk_entidad, clientes.fk_delegacion, clientes.fk_cliente_subzona, clientes.fk_linea_mercado, clientes.fk_forma_pago, clientes.fk_cliente_cond_esp, clientes.fk_provincia_entidad,
                clientes.fk_pais_entidad, clientes.cod_cliente, clientes.bool_es_captacion, clientes.nombre_comercial, clientes.raz_social, clientes.nif, clientes.direccion, clientes.poblacion, clientes.codpostal, clientes.telefono_fijo,
                clientes.telefono_movil, clientes.fax, clientes.mail, clientes.web, clientes.dia_pago, clientes.observaciones, clientes.tipo_iva, clientes.estacionalidad_periodo1_desde, clientes.estacionalidad_periodo1_hasta,
                clientes.estacionalidad_periodo2_desde, clientes.estacionalidad_periodo2_hasta, clientes.bool_asignacion_generica, clientes.varios1, clientes.varios2, clientes.varios3, clientes.varios4, clientes.varios5,
                clientes.varios6, clientes.varios7, clientes.varios8, clientes.varios9, clientes.varios10, clientes.token AS token_cli, clientes.longitud, clientes.latitud, clientes.estado AS estado_cli,

                r_usu_cap.pk_usuario_cliente, r_usu_cap.fk_cliente, r_usu_cap.fk_usuario_vendedor, r_usu_cap.probabilidad, r_usu_cap.fecha_desde, r_usu_cap.fecha_hasta, r_usu_cap.estado AS estado_asi, r_usu_cap.token AS token_asi


             FROM clientes
             JOIN r_usu_cap ON r_usu_cap.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cap.fk_cliente AND fk_usuario_vendedor = '".$userPk."' AND r_usu_cap.estado >= ".$state."
                                AND ((CURDATE() BETWEEN r_usu_cap.fecha_desde AND r_usu_cap.fecha_hasta OR r_usu_cap.fecha_hasta IS NULL) OR ".$onlyActivos." = 0)
             WHERE bool_es_captacion = 1
             AND clientes.fk_entidad = ".$entityId."
             AND (r_usu_cap.updated_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."')
             AND clientes.estado >= ".$state;

        return $q;


    }


    /**
     * Funcion que devuelve las clientes de captaciones asignados a un usuarios (Vendedor o repartidor)
     * paginados y  a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los clientes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param int $state
     * @param null $lastTimeStamp
     * @return array($pagination, array($clientes))
     *
     *
     */
    function getMultipartCachedClientsAssignedToUser($userPk, $entityId, $pagination, $state, $lastTimeStamp, $onlyActive) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $clients = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $onlyActive);

            $query = $this->db->query($query);

            $clients = $query->result();

            $rowcount = sizeof($clients);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            for ($i=0; $i<count($clients); $i++) {
                $r_usu_cap = new r_usu_cap();
                $r_usu_cap->estado = $clients[$i]->estado_asi;
                $r_usu_cap->token = $clients[$i]->token_asi;
                $r_usu_cap->set($clients[$i]);
                $cliente = new cliente();
                $cliente->estado = $clients[$i]->estado_cli;
                $cliente->token = $clients[$i]->token_cli;
                $cliente->set($clients[$i]);
                $clients[$i] = array('cliente' => $cliente, 'r_usu_cap' => $r_usu_cap);
            }

            if ($rowcount > $pagination->pageSize) {
                $chunk_clients = array_chunk($clients, $pagination->pageSize);

                $clients = $chunk_clients[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_clients); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_clients[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "clientes_captacion" => $clients?$clients:array());

    }

    /**
     * Devuelve las captaciones de una entidad
     *
     * @param $entityId
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @return array
     */
    function getAll($entityId, $nomCli, $codCli, $state, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state);
        $this->db->where('bool_es_captacion', 1);
        if ($state > 0) $this->db->where('(fecha_baja IS NULL OR fecha_baja > now())');

        if ($nomCli)
            $this->db->like('nombre_comercial',"$nomCli");
        if ($codCli)
            $this->db->like('cod_cliente',"$codCli");

        if ($offset && $limit)
            $this->db->limit($limit, $offset);

        $query = $this->db->get('clientes');

        $clientes = $query->result('cliente');

        return array("clientes" => $clientes?$clientes:array());

    }

    /**
     * Devuelve la relacion usuario cliente de captacion a partir de su token
     *
     * @param $rUsuCapToken
     * @return mixed
     */
    function getRUsuCapByToken($rUsuCapToken) {
        $this->db->where('token', $rUsuCapToken);
        $query = $this->db->get('r_usu_cap');

        $r_usu_cap = $query->row(0, 'r_usu_cap');
        return $r_usu_cap;
    }

    /**
     * Funcion encagada de guardar en la bbdd r_usu_cap
     *
     * @param $r_usu_cap
     * @return bool
     * @throws APIexception
     */
    function saveRUsuCap($r_usu_cap) {
        $this->load->model("log_model");

        if (!isset($r_usu_cap->token)) {
            $r_usu_cap->token = getToken();
        }

        $result = $r_usu_cap->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on captacion_model->saveRUsuCap. Unable to save r_usu_cap.", ERROR_SAVING_DATA, serialize($r_usu_cap));
        }
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
            $return = 'pk_cliente';
        }

        $this->db->select($return.' AS value,'.$field.' AS description');
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('bool_es_captacion', 1, false);
        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get('clientes');

        $result = $query->result();
        return $result?$result:array();
    }

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param $type
     * @param $userId
     * @return Array(Value, Description)
     */
    function userAssignedsearch($entityId, $field, $query, $return=null, $type='text', $userId) {
        if (!$return) {
            $return = 'pk_cliente';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('clientes');
        $this->db->join('r_usu_cap', 'r_usu_cap.fk_cliente = clientes.pk_cliente');
        $this->db->join('r_usu_emp', 'r_usu_emp.pk_usuario_entidad = r_usu_cap.fk_usuario_vendedor');
        $this->db->where('clientes.fk_entidad', $entityId);
        $this->db->where('r_usu_emp.id_usuario', $userId);
        $this->db->where('bool_es_captacion', 1, false);
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