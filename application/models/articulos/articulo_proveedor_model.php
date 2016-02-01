<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_R_ART_PRO);
require_once(APPPATH.ENTITY_PROVEEDOR);

class articulo_proveedor_model extends CI_Model {


    /**
     * Devuelve la relacion entre un articulo y los preoveedores.
     *
     * @param $entityId
     * @param $state
     * @param $articuloPk
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @return array
     */
    function getAll($entityId, $articuloPk, $state, $offset, $limit) {

        $q = "SELECT id_r_art_pro, r_art_pro.fk_entidad, fk_articulo, fk_proveedor, precio_coste, cod_art_prov, iva, r_art_pro.estado AS estado_r, r_art_pro.token AS token_r,
                pk_proveedor, fk_forma_pago, fk_condicion_pago, cod_proveedor, nombre_comercial, raz_social, nif, direccion, poblacion,
                codpostal, telefono_fijo, telefono_movil, fax, mail, web, persona_contacto, telefono_contacto, mail_contacto, cargo_contacto, dia_pago, observaciones,
                tipo_iva, fk_provincia_entidad, fk_pais_entidad, fk_provincia_entidad_almacen, fk_pais_entidad_almacen, almacen_direccion, almacen_poblacion, almacen_codpostal,
                valoracion_media, logo, pedido_minimo, pro.token AS token_p, pro.estado AS estado_p
                FROM r_art_pro
                JOIN proveedores pro ON pro.fk_entidad = r_art_pro.fk_entidad AND pk_proveedor = fk_proveedor AND pro.estado > 0
                WHERE r_art_pro.fk_entidad = ".$entityId." AND r_art_pro.estado >= ".$state;


        if ($articuloPk)
            $q .= " AND fk_articulo = '" . $articuloPk . "'";

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $q .= " LIMIT " . $limit . " OFFSET " . $offset;

        $query = $this->db->query($q);
        $arts_pro = $query->result();

        $result = array();


        for ($i=0; $i<count($arts_pro); $i++) {


            $r_art_pro =  new r_art_pro();
            $prov = new Proveedor();

            $prov->set($arts_pro[$i]);
            $prov->estado = $arts_pro[$i]->estado_p;
            $prov->token = $arts_pro[$i]->token_p;

            $r_art_pro->set($arts_pro[$i]);
            $r_art_pro->estado = $arts_pro[$i]->estado_r;
            $r_art_pro->token = $arts_pro[$i]->token_r;

            $result[] = array("proveedor" => $prov, "r_art_pro" => $r_art_pro);


        }

        return array("artspro" => $result?$result:array());

    }



    /**
     * Funcion que devuelve la relacion entre articulo proveedor (r_art_pro) de una entidad, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(r_art_pro})
     *
     */
    function getMultipartCached($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $r_arts_pro = unserialize($this->esocialmemcache->get($key));
        } else {

            $q = "SELECT r_art_pro.* FROM r_art_pro
                    JOIN articulos ON articulos.fk_entidad = r_art_pro.fk_entidad AND pk_articulo = fk_articulo AND articulos.estado > 0 AND (fecha_baja IS NULL OR fecha_baja > now())
                    WHERE r_art_pro.fk_entidad = ".$entityId." AND r_art_pro.estado >= ".$state." AND (r_art_pro.updated_at >= '".$lastTimeStamp."' OR articulos.updated_at >= '".$lastTimeStamp."')";

            $query = $this->db->query($q);
            $r_arts_pro = $query->result('r_art_pro');

            $rowcount = sizeof($r_arts_pro);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($r_arts_pro, $pagination->pageSize);

                $r_arts_pro = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "artspro" => $r_arts_pro?$r_arts_pro:array());

    }

    function getArtProByPk($artProPk) {
        $this->db->where('id_r_art_pro', $artProPk);
        $query = $this->db->get('r_art_pro');

        $artPro = $query->row(0, 'r_art_pro');
        return $artPro;
    }

    function getArtProByToken($token) {
        $this->db->where('token', $token);
        $query = $this->db->get('r_art_pro');

        $artPro = $query->row(0, 'r_art_pro');
        return $artPro;
    }

    function getArtsProByEntity($id_entidad) {
        $this->db->where('fk_entidad', $id_entidad);
        $this->db->where('estado = 1');
        $query = $this->db->get('r_art_pro');

        $artsPro = $query->result('r_art_pro');
        return array("artspro" => $artsPro?$artsPro:array());
    }

    /**
     * Guarda la relacion entre articulo y almacen
     *
     * @param $artAlm
     * @return bool
     * @throws APIexception
     */
    function saveArticuloProveedor($artPro) {
        $this->load->model("log_model");

        if (!isset($artPro->token)) {
            $artPro->token = getToken();
        }

        $result = $artPro->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on articulo_proveedor_model->saveArticuloProveedor. Unable to update r_art_pro.", ERROR_SAVING_DATA, serialize($artPro));
        }
    }

}

?>