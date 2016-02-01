<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_ALMACEN);


class proveedor_model extends CI_Model {

    /**
     * Devuelve los proveedores de una entidad
     *
     * @param $entityId
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @param $nombre (Opcional)
     * @param $codigo (Opcional)
     * @return array
     */
    function getAll($entityId, $state, $nombre, $codigo, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state);
        if ($nombre)
            $this->db->like('nombre_comercial', $nombre);
        if ($codigo)
            $this->db->like('cod_proveedor', $codigo);

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $query = $this->db->get('proveedores');

        $result = $query->result('proveedor');

        return array("proveedores" => $result?$result:array());

    }

    /**
     * Funcion que devuelve los proveedores de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(proveedor})
     *
     */
    function getMultipartCached($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $proveedores = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('proveedores');

            $proveedores = $query->result('Proveedor');

            $rowcount = sizeof($proveedores);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($proveedores, $pagination->pageSize);

                $proveedores = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "proveedores" => $proveedores?$proveedores:array());

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
            $return = 'pk_proveedor';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('proveedores');
        $this->db->where('fk_entidad', $entityId);

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(25);
        $query = $this->db->get();

        $result = $query->result();

        return $result?$result:array();
    }
    
    /**
     * 
     * @param $proveedorPk
     * @return the proveedor
     */
    function getProveedorByPk($proveedorPk) {
    	$this->db->where('pk_proveedor', $proveedorPk);
    	$query = $this->db->get('proveedores');
    
    	$proveedor = $query->row(0, 'Proveedor');
    	return $proveedor;
    }
    
    /**
     * 
     * @param $token
     * @return the proveedor
     */
    function getProveedorByToken($token) {
    	$this->db->where('token', $token);
    	$query = $this->db->get('proveedores');
    
    	$proveedor = $query->row(0, 'Proveedor');
    	return $proveedor;
    }
    
    /**
     * Guarda el proveedor en la base de datos
     *
     * @param $proveedor
     * @return bool
     * @throws APIexception
     */
    function saveProveedor($proveedor) {
    	$this->load->model("log_model");
    
    	if (!isset($proveedor->token)) {
    		$proveedor->token = getToken();
    	}
    
    	$result = $proveedor->_save(false, false);
    
    	if ($result) {
    		return true;
    	} else {
    		throw new APIexception("Error on proveedor_model->saveProveedor. Unable to update proveedor.", ERROR_SAVING_DATA, serialize($proveedor));
    	}
    }

    /**
     * Actualiza las valoraciones medias de los proveedores
     */
    function updateValoraciones() {


        $query = "UPDATE proveedores
                    JOIN (
                      SELECT fk_proveedor, fk_entidad, ROUND(AVG(valoracion),2) AS valoracion FROM proveedor_valoracion GROUP BY fk_proveedor, fk_entidad
                    ) valoraciones ON valoraciones.fk_entidad = proveedores.fk_entidad AND fk_proveedor = pk_proveedor
                    SET valoracion_media = valoracion";

        $query = $this->db->query($query);


    }

}

?>