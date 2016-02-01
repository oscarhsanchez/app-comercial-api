<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_VENTA_DIRIGIDA);
require_once(APPPATH.ENTITY_MOTIVO_NO_VENTA_VENTA_DIRIGIDA);


class venta_dirigida_model extends CI_Model {


    /**
     *
     * Funcion que devuelve los registros de la venta dirigida de una entidad, a partir
     * de una fecha de actualizacion.
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param int $state
     * @param null $lastTimeStamp
     * @return array($pagination, array($venta_dirigida))
     *
     *
     */
    function getMultipartCachedVentaDirigida($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $venta_dirigida = unserialize($this->esocialmemcache->get($key));
        } else {
            // EL Join con Lineas de Mercado lo hacemos para filtrar por entidad
            $this->db->select('venta_dirigida.*');
            $this->db->from('venta_dirigida');
            $this->db->join('lineas_mercado', 'venta_dirigida.fk_linea_mercado = lineas_mercado.pk_linea_mercado');
            $this->db->where('lineas_mercado.estado > ', 0);
            $this->db->where('venta_dirigida.estado >= ', $state);
            $this->db->where('venta_dirigida.updated_at > ', $lastTimeStamp);
            $this->db->where('fk_entidad', $entityId);
            $query = $this->db->get();

            $venta_dirigida = $query->result('venta_dirigida');

            $rowcount = sizeof($venta_dirigida);

            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($venta_dirigida, $pagination->pageSize);

                $venta_dirigida = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "venta_dirigida" => $venta_dirigida?$venta_dirigida:array());

    }

    /**
     * Devuelve un motivo de no venta a partir de su token
     *
     * @param $token
     * @param $entityId
     * @return mixed
     */
    function getMotivoNoVentaByToken($token, $entityId) {


        $this->db->where('token', $token);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('mot_no_venta_vd');

        $motivoNoVenta = $query->row(0, 'motivoNoVentaVentaDirigida');

        return $motivoNoVenta;

    }

    /**
     * Funcion que guardar los motivos de no venta que los usuarios envian, de la venta dirigida.
     *
     * @param $motNoVenta
     * @return bool
     * @throws APIexception
     */
    function saveMotivoNoVenta($motNoVenta) {
        $this->load->model("log_model");
        if (!isset($motNoVenta->token)) {
            $motNoVenta->token = getToken();
        }
        $result = $motNoVenta->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on venta_dirigida_model->saveMotivoNoVenta. Unable to update MotivodeNoVentaVD.", ERROR_SAVING_DATA, serialize($motNoVenta));
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
            $return = 'pk_linea_mercado';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('venta_dirigida');
        $this->db->join('lineas_mercado','lineas_mercado.pk_linea_mercado = venta_dirigida.fk_linea_mercado','left');
        $this->db->where('lineas_mercado.fk_entidad', $entityId);

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