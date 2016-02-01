<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_INCIDENCIA);
require_once(APPPATH.ENTITY_REGISTRO_INCIDENCIA);

class cliente_incidencia_model extends CI_Model {


    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT incidencia.*
             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."' AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."')
             JOIN incidencia ON (incidencia.fk_cliente = clientes.pk_cliente OR (incidencia.tipo = 1 AND incidencia.tipo_valor = clientes.pk_cliente)) AND incidencia.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR incidencia.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";

        return $q;
    }

    private function getAssignedRegistrosQuery($entityId, $userPk, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT registro_incidencia.*, CONCAT(usuarios.apellidos, CONCAT(', ',usuarios.nombre)) AS nombre_usuario
             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."' AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."')
             JOIN incidencia ON incidencia.fk_entidad = ".$entityId." AND (incidencia.fk_cliente = clientes.pk_cliente OR (incidencia.tipo = 1 AND incidencia.tipo_valor = clientes.pk_cliente)) AND incidencia.estado >= ".$state."
             JOIN registro_incidencia ON registro_incidencia.incidencia_id = incidencia.id
             LEFT JOIN r_usu_emp ON r_usu_emp.fk_entidad = ".$entityId." AND r_usu_emp.pk_usuario_entidad = incidencia.fk_usuario_asignado
             LEFT JOIN usuarios ON usuarios.id_usuario = r_usu_emp.id_usuario
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR incidencia.updated_at > '".$lastTimeStamp."' OR registro_incidencia.updated_at > '".$lastTimeStamp."')
             AND clientes.estado > 0  ";

        return $q;
    }

    /**
     * Funcion que devuelve las incidencias asociadas a los clientes de un usuario,
     *  a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({$incidencia})
     *
     */
    function getMultipartCachedIncidencias($userPk, $entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $incidencias = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $incidencias = $query->result('incidencia');

            $rowcount = sizeof($incidencias);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($incidencias, $pagination->pageSize);

                $incidencias = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "incidencias" => $incidencias?$incidencias:array());

    }

    /**
     * Funcion que devuelve los registros de las incidencias asociadas a los clientes de un usuario,
     *  a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({$registro_incidencia})
     *
     */
    function getMultipartCachedRegistrosIncidencias($userPk, $entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $registros = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedRegistrosQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $registros = $query->result('registro_incidencia');

            $rowcount = sizeof($registros);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($registros, $pagination->pageSize);

                $registros = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "registros" => $registros?$registros:array());

    }

}

?>