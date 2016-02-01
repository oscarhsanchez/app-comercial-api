<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_ALMACEN);


class almacen_model extends CI_Model {

    /**
     * Devuelve los almacenes de una entidad
     *
     * @param $entityId
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @param $desc (Opcional)
     * @param $codigo (Opcional)
     * @return array
     */
    function getAll($entityId, $state, $desc, $codigo, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state);
        if ($desc)
            $this->db->like('descripcion', $desc);
        if ($codigo)
            $this->db->like('cod_almacen', $codigo);

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $query = $this->db->get('almacen');

        $result = $query->result('almacen');

        return array("almacenes" => $result?$result:array());

    }

    /**
     * Funcion que devuelve los alamcenes disponibles para los tpvs fijos.
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     *
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(almacen})
     *
     */
    function getMultipartCachedTpvAlmacenes($fk_entidad, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $almacenes = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_entidad', $fk_entidad);
            $this->db->where('bool_disponible_tpv', 1);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('almacen');

            $almacenes = $query->result('almacen');

            $rowcount = sizeof($almacenes);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($almacenes, $pagination->pageSize);

                $almacenes = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "almacenes" => $almacenes?$almacenes:array());

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
            $return = 'pk_almacen';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('almacen');
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