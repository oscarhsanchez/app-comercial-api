<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_REPO_CARPETA);


class carpeta_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
    ----------------------------------------*/
    //Solo cogemos las carpetas que tengan al menos un archivo.
    private function getQuery($entityId, $state, $lastTimeStamp) {
        $this->db->select('DISTINCT repo_carpetas.*', false);
        $this->db->from('repo_carpetas');
        $this->db->where('repo_carpetas.fk_entidad', $entityId);
        $this->db->where('repo_carpetas.estado >=', $state, false);
        $this->db->where("(repo_carpetas.updated_at > '".$lastTimeStamp."')" );
    }


    /**
     *
     * Funcion que devuelve las carpetas de un repositorio.
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de las carpetas a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param int $state
     * @param null $lastTimeStamp
     * @return array($pagination, array($repoCarpeta))
     *
     *
     */
    function getMultipartCachedCarpetas($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $carpetas = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->getQuery($entityId, $state, $lastTimeStamp);
            $query = $this->db->get();

            $carpetas = $query->result('repoCarpeta');

            $rowcount = sizeof($carpetas);

            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($carpetas, $pagination->pageSize);

                $carpetas = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "carpetas" => $carpetas?$carpetas:array());

    }
    
    function getCarpetaByNombreAndPadreAndEntidad($nombre, $padre, $entidad) {
    	if (!is_null($padre)) {
	    	$this->db->where('nombre', $nombre);
    	}
    	$this->db->where('fk_carpeta_padre', $padre);
    	$this->db->where('fk_entidad', $entidad);
    	$this->db->from('repo_carpetas');
    	$query = $this->db->get();
    	
    	return $query->row(0, 'repoCarpeta');
    }
    
}

?>