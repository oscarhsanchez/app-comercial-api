<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PRODUCT_SUBFAMILY);
require_once(APPPATH.ENTITY_PRODUCT_FAMILY);
require_once(APPPATH.ENTITY_PRODUCT_GROUP);
require_once(APPPATH.ENTITY_PRODUCT_AGR);
require_once(APPPATH.ENTITY_RESTRICCION_ARTICULO);
require_once(APPPATH.ENTITY_EXCEPTION);

class ruta_model extends CI_Model {


    /**
     * Funcion que devuelve las rutas de los proveedores de una entidad para un codigo postal, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $cosPostal
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(marcaArticulo})
     *
     */
    function getCodPostalMultipartCached($entityId, $codPostal, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $result = unserialize($this->esocialmemcache->get($key));
        } else {
            $q = "SELECT pk_ruta_prov, fk_proveedor, cod_postal, bool_dia1, bool_dia2, bool_dia3, bool_dia4, bool_dia5, bool_dia6, bool_dia7 FROM proveedor_ruta r
                    JOIN codigo_postal c ON r.fk_entidad = c.fk_entidad AND pk_cod_postal = fk_cod_postal AND c.estado > 0
                    WHERE r.fk_entidad = $entityId AND r.estado >= $state AND (r.updated_at >= '$lastTimeStamp' OR c.updated_at >= '$lastTimeStamp') AND cod_postal = '$codPostal'";

            $query = $this->db->query($q);

            $result = $query->result();

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

        return array("pagination" => $pagination, "rutas" => $result?$result:array());

    }

    /**
     * Funcion que devuelve las restricciones de los proveedores de una entidad para un codigo postal, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $cosPostal
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(marcaArticulo})
     *
     */
    function getRestriccionesByCodPostalMultipartCached($entityId, $codPostal, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $result = unserialize($this->esocialmemcache->get($key));
        } else {
            $q = "SELECT pk_zona_prov, fk_proveedor, cod_postal FROM proveedor_restr_zona r
	                JOIN codigo_postal c ON r.fk_entidad = c.fk_entidad AND pk_cod_postal = fk_cod_postal AND c.estado > 0
                    WHERE r.fk_entidad = $entityId AND r.estado >= $state AND (r.updated_at >= '$lastTimeStamp' OR c.updated_at >= '$lastTimeStamp') AND cod_postal = '$codPostal'";

            $query = $this->db->query($q);

            $result = $query->result();

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

        return array("pagination" => $pagination, "restricciones" => $result?$result:array());

    }




}