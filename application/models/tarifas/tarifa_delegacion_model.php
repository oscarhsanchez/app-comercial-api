<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_TARIFA);
require_once(APPPATH.ENTITY_TARIFA_ARTICULO);
require_once(APPPATH.ENTITY_TARIFA_DELEGACION);
require_once(APPPATH.ENTITY_TARIFA_CLIENTE);

class tarifa_delegacion_model extends CI_Model {

    /**
     * Devuelve una tarifa a partir de su token
     *
     * @param $token
     * @return mixed
     */
    function getTarifaDelegacionByToken($token) {
        $this->db->where('token', $token);
        $query = $this->db->get('r_del_tar');

        $tarifa_delegacion = $query->row(0, 'tarifa_delegacion');
        return $tarifa_delegacion;
    }




    /**
     * @param $tarifaDelegacion
     * @return bool
     * @throws APIexception
     */
    function saveTarifaDelegacion($tarifaDelegacion) {
        $this->load->model("log_model");

        if (!isset($tarifaDelegacion->token)) {
            $tarifaDelegacion->token = getToken();
        }

        if (!isset($tarifaDelegacion->pk_cliente)) {
            $tarifaDelegacion->setPk();
        }

        $result = $tarifaDelegacion->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on tarifa_model->saveTarifaDelegacion. Unable to update r_del_tar.", ERROR_SAVING_DATA, serialize($tarifaDelegacion));
        }
    }


    /**
     * Funcion que devuelve la relacion entre las tarifas y una delegacion (r_del_tar), a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $delegacionKey
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(tarifa_delegacion})
     *
     */
    function getMultipartCachedTarifasDelegacion($entityId, $delegacionKey, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $tarifasDel = unserialize($this->esocialmemcache->get($key));
        } else {

            $this->db->where('fk_entidad', $entityId);
            $this->db->where('fk_delegacion', $delegacionKey);
            $this->db->where('estado >=', $state);
            $this->db->where("updated_at > ", $lastTimeStamp );
            $this->db->where("(".$state." = 0 OR fecha_inicio >= CURDATE() OR (CURDATE() BETWEEN fecha_inicio AND fecha_fin))"); //Las presentes y las futuras
            $query = $this->db->get('r_del_tar');

            $tarifasDel = $query->result('tarifa_delegacion');

            $rowcount = sizeof($tarifasDel);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($tarifasDel, $pagination->pageSize);

                $tarifasDel = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "tarifas_delegacion" => $tarifasDel?$tarifasDel:array());

    }




}

?>