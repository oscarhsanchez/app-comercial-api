<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_REPO_ARCHIVO);


class archivo_model extends CI_Model {

    /** ---------------------------------------
     *            FUNCIONES DE APOYO
    ----------------------------------------*/
    private function getQuery($entityId, $state, $lastTimeStamp) {
        $this->db->select('repo_archivos.*');
        $this->db->from('repo_archivos');
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state, false);
        $this->db->where('disponible_en_terminal', 1);
        $this->db->where("(disponible_en_terminal_desde >= CURDATE() OR (CURDATE() BETWEEN disponible_en_terminal_desde AND disponible_en_terminal_hasta))"); //Las presentes y las futuras
        $this->db->where('updated_at >', $lastTimeStamp);
    }


    /**
     *
     * Funcion que devuelve los archivos de un repositorio.
     * paginados y a partir de una fecha de actualizacion.
     * El parametro state establece el estado de los archivos a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param int $state
     * @param null $lastTimeStamp
     * @return array($pagination, array($repoArchivo))
     *
     *
     */
    function getMultipartCachedArchivos($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $archivos = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->getQuery($entityId, $state, $lastTimeStamp);
            $query = $this->db->get();

            $archivos = $query->result('repoArchivo');

            $rowcount = sizeof($archivos);

            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($archivos, $pagination->pageSize);

                $archivos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "archivos" => $archivos?$archivos:array());

    }
    
    /**
     * Guarda un archivo
     * 
     * @param $archivo
     * @throws APIexception
     * @return boolean
     */
    function saveArchivo($archivo) {
    	if (!isset($archivo->token)) {
    		$archivo->token = getToken();
    	} else {
    		$archivoExistente = $this->getArchivoByToken($archivo->token);
    		if ($archivoExistente) {
    			$this->s3->deleteObject('efinanzas', $archivoExistente->aws_key);
    			$archivo->pk_archivo = $archivoExistente->pk_archivo;
    		}
    	}
    	
    	$decodedFile = base64_decode($archivo->fileData);
    	
    	$archivo->aws_key = uploadToAmazon($decodedFile, $archivo->token);
    	$archivo->path = AMAZON_BASE_URL . $archivo->aws_key;
    	
    	$result = $archivo->_save(false, false);
    	
    	if ($result) {
    		return $archivo->path;
    	} else {
    		throw new APIexception("Error on archivo_model->saveArchivo. Unable to update Archivo.", ERROR_SAVING_DATA, serialize($archivo));
    	}
    }
    
    /**
     * Devuelve un archivo buscando por token
     * 
     * @param $token
     * @return $repoArchivo
     * @see repoArchivo
     */
    function getArchivoByToken($token) {
    	$this->db->where('token', $token);
    	$query = $this->db->get('repo_archivos');
    	
    	$repoArchivo = $query->row(0, 'repoArchivo');
    	return $repoArchivo;
    }

}

?>