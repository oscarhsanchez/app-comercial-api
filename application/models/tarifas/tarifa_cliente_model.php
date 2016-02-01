<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_TARIFA);
require_once(APPPATH.ENTITY_TARIFA_ARTICULO);
require_once(APPPATH.ENTITY_TARIFA_DELEGACION);
require_once(APPPATH.ENTITY_TARIFA_CLIENTE);

class tarifa_cliente_model extends CI_Model {

    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT r_cli_tar.*
             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = albaranes_cab.fk_cliente AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."' AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."')
             JOIN r_cli_tar ON r_cli_tar.fk_entidad = clientes.fk_entidad AND r_cli_tar.fk_cliente = clientes.pk_cliente AND r_cli_tar.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId." AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR albaranes_cab.pk_albaran IS NOT NULL )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR r_cli_tar.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0
             AND (".$state." = 0 OR r_cli_tar.fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN r_cli_tar.fecha_inicio AND r_cli_tar.fecha_fin))";

        return $q;
    }

    private function getClientQuery($entityId, $clientePk, $state, $lastTimeStamp) {
        $q = " SELECT r_cli_tar.*
             FROM r_cli_tar
             WHERE r_cli_tar.fk_entidad = ".$entityId."
             AND r_cli_tar.updated_at > '".$lastTimeStamp."'
             AND r_cli_tar.estado >= ".$state." AND fk_cliente = '".$clientePk."'
             AND (".$state." = 0 OR r_cli_tar.fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN r_cli_tar.fecha_inicio AND r_cli_tar.fecha_fin))";

        return $q;
    }

    /**
     * Devuelve una tarifa cliente a partir de su token
     *
     * @param $token
     * @return mixed
     */
    function getTarifaClienteByToken($token) {
        $this->db->where('token', $token);
        $query = $this->db->get('r_cli_tar');

        $tarifaCliente = $query->row(0, 'tarifa_cliente');
        return $tarifaCliente;
    }


    /**
     * @param $tarifaCliente
     * @return bool
     * @throws APIexception
     */
    function saveTarifaCliente($tarifaCliente) {
        $this->load->model("log_model");

        if (!isset($tarifaCliente->token)) {
            $tarifaCliente->token = getToken();
        }

        if (!isset($tarifaCliente->pk_cliente)) {
            $tarifaCliente->setPk();
        }

        $result = $tarifaCliente->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on tarifa_model->saveTarifaCliente. Unable to update r_cli_tar.", ERROR_SAVING_DATA, serialize($tarifaCliente));
        }
    }

    /**
     * Funcion que devuelve la relacion entre las tarifas y los clientes de un usuario (r_cli_tar), a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $userKey
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(tarifa_cliente})
     *
     */
    function getMultipartCachedTarifasCliente($entityId, $userPk, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $tarifasCli = unserialize($this->esocialmemcache->get($key));
        } else {

            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $tarifasCli = $query->result('tarifa_cliente');


            $rowcount = sizeof($tarifasCli);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($tarifasCli, $pagination->pageSize);

                $tarifasCli = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "tarifas_clientes" => $tarifasCli?$tarifasCli:array());

    }

    /**
     * Funcion que devuelve la relacion entre las tarifas y los clientes para un cliente en concreto (r_cli_tar), a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $clientePk
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(tarifa_cliente})
     *
     */
    function getMultipartCachedTarifasClienteByCliente($entityId, $clientePk, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $tarifasCli = unserialize($this->esocialmemcache->get($key));
        } else {

            $query = $this->getClientQuery($entityId, $clientePk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $tarifasCli = $query->result('tarifa_cliente');


            $rowcount = sizeof($tarifasCli);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($tarifasCli, $pagination->pageSize);

                $tarifasCli = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "tarifas_clientes" => $tarifasCli?$tarifasCli:array());

    }


}

?>