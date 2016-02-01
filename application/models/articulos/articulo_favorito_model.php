<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PRODUCT_SUBFAMILY);
require_once(APPPATH.ENTITY_PRODUCT_FAMILY);
require_once(APPPATH.ENTITY_PRODUCT_GROUP);
require_once(APPPATH.ENTITY_PRODUCT_AGR);
require_once(APPPATH.ENTITY_ARTICULO_FAVORITO);
require_once(APPPATH.ENTITY_EXCEPTION);

class articulo_favorito_model extends CI_Model {


    function getFavoritoByToken($entityId, $favoritoToken) {
        $this->db->where('token', $favoritoToken);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('cliente_art_favorito');

        $product = $query->row(0, 'articuloFavorito');
        return $product;
    }

    /**
     * Devuelve los articulos favoritos de un cliente
     *
     * @param $entityId
     * @param $clientePk
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @return array
     */
    function getAll($entityId, $clientePk, $state, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('fk_cliente', $clientePk);
        $this->db->where('estado >=', $state);

        if ($offset && $limit)
            $this->db->limit($limit, $offset);

        $query = $this->db->get('cliente_art_favorito');

        $favoritos = $query->result('articuloFavorito');

        return array("favoritos" => $favoritos?$favoritos:array());

    }

    /**
     * Funcion que devuelve los articulos favoritos de un cliente, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $clientePk
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articuloFavorito})
     *
     */
    function getMultipartCached($entityId, $clientePk, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $favoritos = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('fk_cliente', $clientePk);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('cliente_art_favorito');

            $favoritos = $query->result('articuloFavorito');

            $rowcount = sizeof($favoritos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($favoritos, $pagination->pageSize);

                $favoritos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "favoritos" => $favoritos?$favoritos:array());

    }

    /**
     * Guarda un articulo favorito de un cliente.
     *
     * @param $favorito
     * @return bool
     * @throws APIexception
     */
    function save($favorito) {
        $this->load->model("log_model");

        if (!isset($favorito->token)) {
            $favorito->token = getToken();
        }

        $result = $favorito->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on articulo_favorito_model->save. Unable to update Favorito.", ERROR_SAVING_DATA, serialize($favorito));
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
            $return = 'pk_favorito';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('cliente_art_favorito');
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