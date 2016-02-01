<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PRODUCT_SUBFAMILY);
require_once(APPPATH.ENTITY_PRODUCT_FAMILY);
require_once(APPPATH.ENTITY_PRODUCT_GROUP);
require_once(APPPATH.ENTITY_PRODUCT_AGR);
require_once(APPPATH.ENTITY_R_ART_AGR);
require_once(APPPATH.ENTITY_EXCEPTION);

class articulo_clasificacion_model extends CI_Model {

    /**
     * Funcion que devuelve las subfamilias de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articuloSubFamilia})
     *
     */
    function getMultipartCachedSubfamilias($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $subfams = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('art_subfamilias');

            $subfams = $query->result('articuloSubFamilia');

            $rowcount = sizeof($subfams);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($subfams, $pagination->pageSize);

                $subfams = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "subfamilias" => $subfams?$subfams:array());

    }

    /**
     * Funcion que devuelve las familias de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articuloFamilia})
     *
     */
    function getMultipartCachedFamilias($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $fams = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('art_familias');

            $fams = $query->result('articuloFamilia');

            $rowcount = sizeof($fams);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($fams, $pagination->pageSize);

                $fams = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "familias" => $fams?$fams:array());

    }

    /**
     * Funcion que devuelve los grupos de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articuloGrupo})
     *
     */
    function getMultipartCachedGrupos($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $grupos = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('art_grupos');

            $grupos = $query->result('articuloGrupo');

            $rowcount = sizeof($grupos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($grupos, $pagination->pageSize);

                $grupos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "grupos" => $grupos?$grupos:array());

    }

    /**
     * Funcion que devuelve los grupos de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articuloGrupo})
     *
     */
    function getMultipartCachedAgrs($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $agrs = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('art_agrupaciones');

            $agrs = $query->result('articuloAgr');

            $rowcount = sizeof($agrs);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($agrs, $pagination->pageSize);

                $agrs = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "agrs" => $agrs?$agrs:array());

    }

    /**
     * Funcion que devuelve las R_ART_AGR de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(articuloGrupo})
     *
     */
    function getMultipartCachedRArtAgr($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $agrs_arts = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->select('r_art_agr.*');
            $this->db->from('r_art_agr');
            $this->db->join('art_agrupaciones', 'r_art_agr.fk_agrupacion = art_agrupaciones.pk_art_agrupaciones');
            $this->db->where('fk_entidad', $entityId);
            $this->db->where('r_art_agr.estado >=', $state);
            $this->db->where('art_agrupaciones.estado >=', 1);
            $this->db->where('r_art_agr.updated_at >=', $lastTimeStamp);
            $query = $this->db->get();

            $agrs_arts = $query->result('r_art_agr');

            $rowcount = sizeof($agrs_arts);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($agrs_arts, $pagination->pageSize);

                $agrs_arts = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "r_art_agr" => $agrs_arts?$agrs_arts:array());

    }


    function getSubFamilyByPk($subFamilyPk) {
		$this->db->where('pk_art_subfamilias', $subFamilyPk);
		$query = $this->db->get('art_subfamilias'); 		

		$result = $query->row(0, 'articuloSubFamilia');
		return $result;
	}

	function getSubFamilyByToken($subFamilyToken) {
		$this->db->where('token', $subFamilyToken);
		$query = $this->db->get('art_subfamilias'); 		

		$result = $query->row(0, 'articuloSubFamilia');
		return $result;
	}

	function getFamilyByPk($familyPk) {
		$this->db->where('pk_art_familias', $familyPk);
		$query = $this->db->get('art_familias'); 		

		$result = $query->row(0, 'articuloFamilia');
		return $result;
	}

	function getFamilyByToken($familyToken) {
		$this->db->where('token', $familyToken);
		$query = $this->db->get('art_familias'); 		

		$result = $query->row(0, 'articuloFamilia');
		return $result;
	}

	function getGroupByPk($groupPk) {
		$this->db->where('pk_art_grupos', $groupPk);
		$query = $this->db->get('art_grupos'); 		

		$result = $query->row(0, 'articuloGrupo');
		return $result;
	}

	function getGroupByToken($groupToken) {
		$this->db->where('token', $groupToken);
		$query = $this->db->get('art_grupos'); 		

		$result = $query->row(0, 'articuloGrupo');
		return $result;
	}

	function getAgrByPk($agrPk) {
		$this->db->where('pk_art_agrupaciones', $agrPk);
		$query = $this->db->get('art_agrupaciones'); 		

		$result = $query->row(0, 'articuloAgr');
		return $result;
	}

	function getAgrByToken($agrToken) {
		$this->db->where('token', $agrToken);
		$query = $this->db->get('art_agrupaciones'); 		

		$result = $query->row(0, 'articuloAgr');
		return $result;
	}

	function getRartAgrByPk($pk) {
		$this->db->where('id', $pk);
		$query = $this->db->get('r_art_agr');
	
		$result = $query->row(0, 'r_art_agr');
		return $result;
	}
	
	function getRartAgrByToken($token) {
		$this->db->where('token', $token);
		$query = $this->db->get('r_art_agr');
	
		$result = $query->row(0, 'r_art_agr');
		return $result;
	}

	function saveSubFamily($subFamily) {
		$this->load->model("log_model");

		if (!isset($subFamily->token)) {
			$subFamily->token = getToken();
		}

		if (!isset($subFamily->pk_art_subfamilias)) {
			$subFamily->setPk();
		}

		$result = $subFamily->_save(false, false);

		if ($result) {
			return true;
		} else {
			throw new APIexception("Error on articulo_clasificacion_model->saveSubFamily. Unable to update SubFamily", ERROR_SAVING_DATA, serialize($subFamily));
		}
	}

	function saveFamily($family) {
		$this->load->model("log_model");

		if (!isset($family->token)) {
			$family->token = getToken();
		}

		if (!isset($family->pk_art_familias)) {
			$family->setPk();
		}

		$result = $family->_save(false, false);

		if ($result) {
			return true;
		} else {
			throw new APIexception("Error on articulo_clasificacion_model->saveFamily. Unable to update Family", ERROR_SAVING_DATA, serialize($family));
		}
	}

	function saveGroup($group) {
		$this->load->model("log_model");

		if (!isset($group->token)) {
			$group->token = getToken();
		}

		if (!isset($group->pk_art_grupos)) {
			$group->setPk();
		}

		$result = $group->_save(false, false);

		if ($result) {
			return true;
		} else {
			throw new APIexception("Error on articulo_clasificacion_model->saveGroup. Unable to update group", ERROR_SAVING_DATA, serialize($group));
		}
	}

	function saveAgr($agr) {
		$this->load->model("log_model");

		if (!isset($agr->token)) {
			$agr->token = getToken();
		}

		if (!isset($agr->pk_art_agrupaciones)) {
			$agr->setPk();
		}

		$result = $agr->_save(false, false);

		if ($result) {
			return true;
		} else {
			throw new APIexception("Error on articulo_clasificacion_model->saveAgr. Unable to update Product Agr.", ERROR_SAVING_DATA, serialize($agr));
		}
	}
	
	function saveArtAgr($artAgr) {
		$this->load->model("log_model");
	
		if (!isset($artAgr->token)) {
			$artAgr->token = getToken();
		}
	
		$result = $artAgr->_save(false, true);
	
		if ($result) {
			return true;
		} else {
			throw new APIexception("Error on articulo_clasificacion_model->saveArtAgr.", ERROR_SAVING_DATA, serialize($artAgr));
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
    function agrSearch($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_art_agrupaciones';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('art_agrupaciones');
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

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function grupoSearch($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_art_grupos';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('art_grupos');
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

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function familiaSearch($entityId, $field, $query, $return=null, $type='text', $grupoId=null) {
        if (!$return) {
            $return = 'pk_art_familias';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('art_familias');
        $this->db->where('fk_entidad', $entityId);

        if ($grupoId) {
            $this->db->where('fk_grupo', $grupoId);
        }

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function subfamiliaSearch($entityId, $field, $query, $return=null, $type='text', $familiaId) {
        if (!$return) {
            $return = 'pk_art_subfamilias';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('art_subfamilias');
        $this->db->where('fk_entidad', $entityId);

        if ($familiaId) {
            $this->db->where('fk_familia', $familiaId);
        }

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