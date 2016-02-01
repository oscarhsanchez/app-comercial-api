<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_REFERENCIA_MPV);
require_once(APPPATH.ENTITY_TIPO_MPV);
require_once(APPPATH.ENTITY_R_DEL_MPV);

class cliente_mpv_model extends CI_Model {


    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT referencia_mpv.*
             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0 AND visita.fecha_visita >= CURDATE()
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."' AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."')
             JOIN referencia_mpv ON referencia_mpv.fk_cliente = clientes.pk_cliente AND referencia_mpv.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR referencia_mpv.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";

        return $q;
    }

    private function getEntidadAssignedQuery($entityId, $state, $lastTimeStamp) {
        $q = " SELECT DISTINCT referencia_mpv.*
             FROM clientes
             JOIN referencia_mpv ON referencia_mpv.fk_cliente = clientes.pk_cliente AND referencia_mpv.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (clientes.updated_at > '".$lastTimeStamp."' OR referencia_mpv.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0  ";

        return $q;
    }

    private function getTiposQuery($entityId, $delegacionPk, $state, $lastTimeStamp) {
        $q = " SELECT tipo_mpv.*, delegacion_stock.id AS delegacionStock_id, stock_total AS stock FROM tipo_mpv
                JOIN delegacion_stock ON delegacion_stock.fk_tipo_mpv = tipo_mpv.id AND delegacion_stock.estado > 0
                WHERE tipo_mpv.estado >= ".$state." AND (tipo_mpv.updated_at > '".$lastTimeStamp."' OR delegacion_stock.updated_at > '".$lastTimeStamp."')
                AND delegacion_stock.fk_delegacion = '".$delegacionPk."' AND fk_entidad = ".$entityId;

        return $q;
    }

    /**
     * Devuelve un tipo de MPV a partir de su token
     *
     * @param $tipoMpvToken
     * @return mixed
     */
    function getTipoMpvByToken($tipoMpvToken) {
        $this->db->where('token', $tipoMpvToken);
        $query = $this->db->get('tipo_mpv');

        $tipoMpv = $query->row(0, 'tipo_mpv');
        return $tipoMpv;
    }

    /**
     * Devuelve un tipo de MPV a partir de su codigo
     *
     * @param $codTipoMpv
     * @param $fk_entidad
     * @return mixed
     */
    function getTipoMpvByCod($codTipoMpv, $fk_entidad) {
        $this->db->where('cod_tipo_mpv', $codTipoMpv);
        $this->db->where('fk_entidad', $fk_entidad);
        $query = $this->db->get('tipo_mpv');

        $tipoMpv = $query->row(0, 'tipo_mpv');
        return $tipoMpv;
    }

    /**
     * Devuelve la relacion entre una delegacion y un tipo MPV a partir de su token
     *
     * @param $rDelMpv
     * @return mixed
     */
    function getRDelMpvByToken($rDelMpv) {
        $this->db->where('token', $rDelMpv);
        $query = $this->db->get('delegacion_stock');

        $result = $query->row(0, 'r_del_mpv');
        return $result;
    }

    /**
     * Devuelve la relacion entre una delegacion y un tipo MPV a partir de su codigo
     *
     * @param $codDelegacionStock
     * @param $fk_entidad
     * @return mixed
     */
    function getRDelMpvByCod($codDelegacionStock, $fk_entidad) {
        $this->db->select('delegacion_stock.*');
        $this->db->from('delegacion_stock');
        $this->db->join('delegaciones', 'delegaciones.pk_delegacion = delegacion_stock.fk_delegacion');
        $this->db->where('cod_delegacion_stock', $codDelegacionStock);
        $this->db->where('fk_entidad', $fk_entidad);
        $query = $this->db->get();

        $result = $query->row(0, 'r_del_mpv');
        return $result;
    }

    /**
     * Devuelve la relacion entre un cliente y un tipo MPV a partir de su token
     *
     * @param $referenciaMpv
     * @return mixed
     */
    function getReferenciaMpvByToken($referenciaMpv) {
        $this->db->where('token', $referenciaMpv);
        $query = $this->db->get('referencia_mpv');

        $result = $query->row(0, 'referencia_mpv');
        return $result;
    }

    /**
     * Funcion encagada de guardar en la bbdd un tipo de MPV
     *
     * @param $tipoMpv
     * @return bool
     * @throws APIexception
     */
    function saveTipoMpv($tipoMpv) {
        $this->load->model("log_model");

        if (!isset($tipoMpv->token)) {
            $tipoMpv->token = getToken();
        }

        $result = $tipoMpv->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cliente_mpv_model->saveTipoMpv. Unable to save tipoMpv.", ERROR_SAVING_DATA, serialize($tipoMpv));
        }
    }

    /**
     * Funcion encagada de guardar en la bbdd la relacion entre una delegacion y un tipo de MPV
     *
     * @param $rDelMpv
     * @return bool
     * @throws APIexception
     */
    function saveRDelMpv($rDelMpv) {
        $this->load->model("log_model");

        if (!isset($rDelMpv->token)) {
            $rDelMpv->token = getToken();
        }

        $result = $rDelMpv->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cliente_mpv_model->saveRDelMpv. Unable to save rDelMpv.", ERROR_SAVING_DATA, serialize($rDelMpv));
        }
    }

    /**
     * Funcion encagada de guardar en la bbdd la relacion entre un cliente y un tipo de MPV
     *
     * @param $referenciaMpv
     * @return bool
     * @throws APIexception
     */
    function saveReferenciaMpv($referenciaMpv) {
        $this->load->model("log_model");

        if (!isset($referenciaMpv->token)) {
            $referenciaMpv->token = getToken();
        }

        $result = $referenciaMpv->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on cliente_mpv_model->saveReferenciaMpv. Unable to save referencia_mpv.", ERROR_SAVING_DATA, serialize($referenciaMpv));
        }
    }

    /**
     * Funcion que devuelve las referencia de MPV de una entidad,
     *  a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({$referencia_mpv})
     *
     */
    function getEntidadMultipartCachedReferenciaMpv($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $referencias = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getEntidadAssignedQuery($entityId, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $referencias = $query->result('referencia_mpv');

            $rowcount = sizeof($referencias);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($referencias, $pagination->pageSize);

                $referencias = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "referencias" => $referencias?$referencias:array());

    }

    /**
     * Funcion que devuelve las referencia de MPV asociadas a los clientes de un usuario,
     *  a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({$referencia_mpv})
     *
     */
    function getMultipartCachedReferenciaMpv($userPk, $entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $referencias = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $referencias = $query->result('referencia_mpv');

            $rowcount = sizeof($referencias);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($referencias, $pagination->pageSize);

                $referencias = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "referencias" => $referencias?$referencias:array());

    }

    /**
     * Funcion que devuelve los tipos de MPV de la delegacion,
     *  a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $delegacionPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array({$tipo_mpv})
     *
     */
    function getMultipartCachedTiposMpv($delegacionPk, $entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $tiposMpv = unserialize($this->esocialmemcache->get($key));
        } else {
            $query = $this->getTiposQuery($entityId, $delegacionPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $tiposMpv = $query->result('tipo_mpv');

            $rowcount = sizeof($tiposMpv);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($tiposMpv, $pagination->pageSize);

                $tiposMpv = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "tiposMpv" => $tiposMpv?$tiposMpv:array());

    }
}

?>