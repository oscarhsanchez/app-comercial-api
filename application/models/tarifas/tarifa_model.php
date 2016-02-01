<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_TARIFA);
require_once(APPPATH.ENTITY_TARIFA_ARTICULO);
require_once(APPPATH.ENTITY_TARIFA_DELEGACION);
require_once(APPPATH.ENTITY_TARIFA_CLIENTE);

class tarifa_model extends CI_Model {
    // (0= ".$state." OR fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN fecha_inicio AND fecha_fin)) --> La primera vez devolvemos solo las activas y el resto todas para poder eliminar por la fecha en el terminal
    private function getAssignedQuery($entityId, $userPk, $delegacionPk, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT tarifas.*
             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = albaranes_cab.fk_cliente AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."') AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."'
             JOIN r_cli_tar ON r_cli_tar.fk_entidad = clientes.fk_entidad AND r_cli_tar.fk_cliente = clientes.pk_cliente AND r_cli_tar.estado > 0
             JOIN tarifas ON tarifas.fk_entidad = clientes.fk_entidad AND tarifas.pk_tarifa = r_cli_tar.fk_tarifa AND tarifas.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId." AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR albaranes_cab.pk_albaran IS NOT NULL )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR r_cli_tar.updated_at > '".$lastTimeStamp."' OR tarifas.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0 AND (0= ".$state." OR r_cli_tar.fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN r_cli_tar.fecha_inicio AND r_cli_tar.fecha_fin))
             UNION
             SELECT DISTINCT tarifas.*
             FROM tarifas
             JOIN r_del_tar ON tarifas.fk_entidad = r_del_tar.fk_entidad AND tarifas.pk_tarifa = r_del_tar.fk_tarifa AND r_del_tar.estado > 0
             WHERE tarifas.fk_entidad = ".$entityId." AND (0= ".$state." OR fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN fecha_inicio AND fecha_fin))
             AND (tarifas.updated_at > '" . $lastTimeStamp . "' OR r_del_tar.updated_at > '" . $lastTimeStamp ."')
             AND tarifas.estado >= ".$state." AND fk_delegacion = '".$delegacionPk."'

             ";

        return $q;
    }

    private function getClienteQuery($entityId, $clientePk, $delegacionPk, $state, $lastTimeStamp) {
        $q = "SELECT DISTINCT tarifas.*
                FROM tarifas
                JOIN r_cli_tar ON r_cli_tar.fk_entidad = tarifas.fk_entidad AND r_cli_tar.fk_tarifa = tarifas.pk_tarifa AND r_cli_tar.estado > 0
                WHERE tarifas.fk_entidad = 27 AND r_cli_tar.fk_cliente = '".$clientePk."' AND tarifas.estado >= ".$state." and (r_cli_tar.fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN r_cli_tar.fecha_inicio AND r_cli_tar.fecha_fin))
                AND (r_cli_tar.updated_at > '".$lastTimeStamp."' OR tarifas.updated_at > '".$lastTimeStamp."' )
             UNION
              SELECT DISTINCT tarifas.*
                FROM tarifas
                JOIN r_del_tar ON tarifas.fk_entidad = r_del_tar.fk_entidad AND tarifas.pk_tarifa = r_del_tar.fk_tarifa AND r_del_tar.estado > 0
                WHERE tarifas.fk_entidad = ".$entityId." AND (fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN fecha_inicio AND fecha_fin))
                AND (tarifas.updated_at > '" . $lastTimeStamp . "' OR r_del_tar.updated_at > '" . $lastTimeStamp ."')
                AND tarifas.estado >= ".$state." AND fk_delegacion = '".$delegacionPk."'
             ";

        return $q;
    }

    /**
     * Devuelve una tarifa a partir de su token
     *
     * @param $token
     * @return mixed
     */
    function getTarifaByToken($token) {
        $this->db->where('token', $token);
        $query = $this->db->get('tarifas');

        $tarifa = $query->row(0, 'tarifa');
        return $tarifa;
    }

    /**
     * @param $tarifa
     * @return bool
     * @throws APIexception
     */
    function saveTarifa($tarifa) {
        $this->load->model("log_model");

        if (!isset($tarifa->token)) {
            $tarifa->token = getToken();
        }

        if (!isset($tarifa->pk_cliente)) {
            $tarifa->setPk();
        }

        $result = $tarifa->_save(false, false);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on tarifa_model->saveTarifa. Unable to update Tarifa.", ERROR_SAVING_DATA, serialize($tarifa));
        }
    }

    /**
     * Funcoin que devuelve las tarifas para un cliente concerto.
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $clientePk
     * @param $delegacionPk
     * @param $state
     * @param $pagination
     * @param $lastTimeStamp
     */
    function getTarifasByCliente($entityId, $clientePk, $delegacionPk, $state, $pagination, $lastTimeStamp)  {
        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $tarifas = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getClienteQuery($entityId, $clientePk, $delegacionPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $tarifas = $query->result('tarifa');

            $rowcount = sizeof($tarifas);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($tarifas, $pagination->pageSize);

                $tarifas = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "tarifas" => $tarifas?$tarifas:array());
    }



    /**
     * Funcion que devuelve las tarifas asociadas a los clientes de un unsuario, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $delegacionKey
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(tarifa_articulo})
     *
     */
    function getMultipartCachedTarifas($entityId, $userPk, $delegacionKey, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $tarifas = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $delegacionKey, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $tarifas = $query->result('tarifa');

            $rowcount = sizeof($tarifas);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($tarifas, $pagination->pageSize);

                $tarifas = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "tarifas" => $tarifas?$tarifas:array());

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
            $return = 'pk_tarifa';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('tarifas');
        $this->db->where('tarifas.fk_entidad', $entityId);

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