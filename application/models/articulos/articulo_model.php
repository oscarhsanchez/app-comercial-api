<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PRODUCT);
require_once(APPPATH.ENTITY_PRODUCT_IMG);


class articulo_model extends CI_Model {

    function getArticuloByPk($entityId, $productPk) {
        $this->db->where('pk_articulo', $productPk);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('articulos');

        $product = $query->row(0, 'articulo');
        return $product;
    }

	function getProductByPk($productPk) {
		$this->db->where('pk_articulo', $productPk);
		$query = $this->db->get('articulos');

		$product = $query->row(0, 'articulo');
		return $product;
	}

	function getProductByToken($productToken) {
		$this->db->where('token', $productToken);
		$query = $this->db->get('articulos'); 		

		$product = $query->row(0, 'articulo');
		return $product;
	}

    /**
     * Devuelve los articulos de una entidad
     *
     * @param $entityId
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @param $subfamiliaPk (Opcional)
     * @param $desc (Opcional)
     * @param $codigo (Opcional)
     * @param $ean (Opcional)
     * @return array
     */
    function getAll($entityId, $subfamiliaPk, $state, $desc, $codigo, $ean, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state);
        if ($subfamiliaPk)
            $this->db->where('fk_subfamilia =', $subfamiliaPk);
        if ($desc)
            $this->db->like('descripcion', $desc);
        if ($codigo)
            $this->db->like('cod_articulo', $codigo);
        if ($ean)
            $this->db->like('codigo_ean', $ean);


        $this->db->where('(fecha_baja IS NULL OR fecha_baja > now())');

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $query = $this->db->get('articulos');

        $articulos = $query->result('articulo');

        return array("articulos" => $articulos?$articulos:array());

    }

    /**
     * Funcion que devuelve los articulos de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articulo})
     *
     */
    function getMultipartCachedArticulos($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $articulos = unserialize($this->esocialmemcache->get($key));
            if (!$articulos) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
            $this->esocialmemcache->delete($key);
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            if ($state > 0) $this->db->where('fecha_baja IS NULL OR fecha_baja > now()');
            $query = $this->db->get('articulos');

            $articulos = $query->result('articulo');

            $rowcount = sizeof($articulos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_clients = array_chunk($articulos, $pagination->pageSize);

                $articulos = $chunk_clients[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_clients); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_clients[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "articulos" => $articulos?$articulos:array());

    }

    /**
     * Funcion que devuelve las imagenes de un articulo.
     *
     * @param $articuloPk
     * @return array(articuloImagen)
     */
    function getArticuloImagenes($entityId, $articuloPk) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('fk_articulo', $articuloPk);
        $this->db->where('estado', 1);
        $query = $this->db->get('art_imagenes');

        $imagenes = $query->result('articuloImagen');

        return array("imagenes" => $imagenes?$imagenes:array());

    }

    /**
     * Funcion que devuelve las imagenes de los articulos de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articulo})
     *
     */
    function getMultipartCachedImagenesArticulos($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $imagenes = unserialize($this->esocialmemcache->get($key));
            if (!$imagenes) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
            $this->esocialmemcache->delete($key);
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);

            $query = $this->db->get('art_imagenes');

            $imagenes = $query->result('articuloImagen');

            $rowcount = sizeof($imagenes);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_clients = array_chunk($imagenes, $pagination->pageSize);

                $imagenes = $chunk_clients[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_clients); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_clients[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "imagenes" => $imagenes?$imagenes:array());

    }

	function saveProduct($product, $omittedFields=null) {
		$this->load->model("log_model");

		if (!isset($product->token)) {
			$product->token = getToken();
		}

		if (!isset($product->pk_articulo)) {
			$product->setPk();
		}

		$result = $product->_save(false, false, $omittedFields);

		if ($result) {
			return true;
		} else {
			throw new APIexception("Error on articulo_model->saveProduct. Unable to update Product.", ERROR_SAVING_DATA, serialize($product));
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
            $return = 'pk_articulo';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('articulos');
        $this->db->where('articulos.fk_entidad', $entityId);

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