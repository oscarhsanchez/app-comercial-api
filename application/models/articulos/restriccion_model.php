<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PRODUCT_SUBFAMILY);
require_once(APPPATH.ENTITY_PRODUCT_FAMILY);
require_once(APPPATH.ENTITY_PRODUCT_GROUP);
require_once(APPPATH.ENTITY_PRODUCT_AGR);
require_once(APPPATH.ENTITY_RESTRICCION_ARTICULO);
require_once(APPPATH.ENTITY_EXCEPTION);

class restriccion_model extends CI_Model {


    /**
     * Funcion que devuelve las restricciones de un cliente, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $clientePk
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(marcaArticulo})
     *
     */
    function getClientMultipartCached($entityId, $clientePk, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $result = unserialize($this->esocialmemcache->get($key));
        } else {
            //$this->db->where('fk_entidad', $entityId);
            $this->db->where('fk_cliente', $clientePk);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('art_restriccion');

            $result = $query->result('restriccionArticulo');

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

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function restriccionSearch($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_marca_articulo';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('art_restriccion');
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