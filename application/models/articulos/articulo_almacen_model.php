<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_R_ART_ALM);
require_once(APPPATH.ENTITY_R_ART_PRO);
require_once(APPPATH.ENTITY_ALMACEN);
require_once(APPPATH.ENTITY_PRODUCT);
require_once(APPPATH.ENTITY_INVENTARIO);
require_once(APPPATH.ENTITY_INVENTARIO_LIN);
require_once(APPPATH.ENTITY_MOVIMIENTO_ALMACEN);
require_once(APPPATH.ENTITY_MOVIMIENTO_ALMACEN_LIN);


class articulo_almacen_model extends CI_Model {

    function getArtAlmByPk($artAlmPk) {
        $this->db->where('id_r_art_alm', $$artAlmPk);
        $query = $this->db->get('r_art_alm');

        $artAlm = $query->row(0, 'r_art_alm');
        return $artAlm;
    }

    function getArtAlmByToken($token) {
        $this->db->where('token', $token);
        $query = $this->db->get('r_art_alm');

        $artAlm = $query->row(0, 'r_art_alm');
        return $artAlm;
    }

    function getArticuloByAlmacenAndArticulo($entityId, $almacenPk, $articuloPk) {
        $q = "SELECT *, ralm.estado AS estado_ralm, art.estado AS estado_art, ralm.token AS token_ralm, art.token AS token_art
                FROM r_art_alm ralm
                JOIN articulos art ON art.fk_entidad = ralm.fk_entidad AND pk_articulo = ralm.fk_articulo AND art.estado >= 0
              WHERE ralm.fk_entidad = $entityId AND fk_almacen = '$almacenPk' AND ralm.estado > 0 AND fk_articulo = '$articuloPk'
        ";

        $query = $this->db->query($q);

        $result = $query->row();

        if ($result) {
            $articulo = new articulo();
            $r_art_alm = new r_art_alm();

            $articulo->set($result);
            $articulo->estado = $result->estado_art;
            $articulo->token = $result->token_art;

            $r_art_alm->set($result);
            $r_art_alm->estado = $result->estado_ralm;
            $r_art_alm->token = $result->token_ralm;

            return Array("articulo" => $articulo, "r_art_alm" => $r_art_alm);


        }   else {
            return Array("articulo" => null, "r_art_alm" => null);
        }

    }

    function getArtAlmByAlmacenAndArticulo($entityId, $almacenPk, $articuloPk) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('fk_almacen', $almacenPk);
        $this->db->where('fk_articulo', $articuloPk);
        $query = $this->db->get('r_art_alm');

        $artAlm = $query->row(0, 'r_art_alm');
        return $artAlm;
    }

    function getArtAlmByArticulo($fk_entidad, $articuloPk) {
        $this->db->where('fk_entidad', $fk_entidad);
        $this->db->where('fk_articulo', $articuloPk);
        $query = $this->db->get('r_art_alm');

        $result = $query->result('r_art_alm');

        return array("arts_alm" => $result?$result:array());

    }

    /**
     * Devuelve un listado de articulos y su relacion con el almacen indicado.
     *
     * @param $entityId
     * @param $state
     * @param $almacenPk
     * @param $proveedorPk (Opcional)
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @return array
     */
    function getAll($entityId, $almacenPk, $proveedorPk, $desc, $codigo, $ean, $state, $offset, $limit) {

        $q = "SELECT *, ralm.estado AS estado_ralm, art.estado AS estado_art, ralm.token AS token_ralm, art.token AS token_art ";

        if ($proveedorPk)
            $q .= " ,rpro.estado AS estado_rpro, rpro.token AS token_rpro ";

        $q .= "FROM r_art_alm ralm
               JOIN articulos art ON art.fk_entidad = ralm.fk_entidad AND pk_articulo = ralm.fk_articulo AND art.estado >= $state ";

        if ($proveedorPk)
            $q .= "JOIN r_art_pro rpro ON rpro.fk_entidad = ralm.fk_entidad AND ralm.fk_articulo = rpro.fk_articulo AND rpro.estado >= $state AND fk_proveedor = '$proveedorPk' ";

        $q .= "WHERE ralm.fk_entidad = $entityId AND fk_almacen = '$almacenPk' AND ralm.estado >= $state ";

        if ($desc)
            $q .= " AND art.descripcion LIKE '%$desc%'";

        if ($codigo)
            $q .= " AND art.cod_articulo LIKE '%$codigo%'";

        if ($ean)
            $q .= " AND art.codigo_ean LIKE '%$ean%'";


        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $q .= " LIMIT " . $limit . " OFFSET " . $offset;

        $query = $this->db->query($q);
        $arts = $query->result();

        $result = array();


        for ($i=0; $i<count($arts); $i++) {


            $r_art_alm =  new r_art_alm();
            $r_art_pro =  new r_art_pro();
            $articulo = new articulo();

            $articulo->set($arts[$i]);
            $articulo->estado = $arts[$i]->estado_art;
            $articulo->token = $arts[$i]->token_art;

            $r_art_alm->set($arts[$i]);
            $r_art_alm->estado = $arts[$i]->estado_ralm;
            $r_art_alm->token = $arts[$i]->token_ralm;

            if ($proveedorPk) {
                $r_art_pro->set($arts[$i]);
                $r_art_pro->estado = $arts[$i]->estado_rpro;
                $r_art_pro->token = $arts[$i]->token_rpro;

                $result[] = array("articulo" => $articulo, "r_art_alm" => $r_art_alm, "r_art_pro" => $r_art_pro);
            } else
                $result[] = array("articulo" => $articulo, "r_art_alm" => $r_art_alm);


        }

        return array("articulos" => $result?$result:array());

    }

    function getHistoricoByAlmacenAndArticulo($fk_entidad, $almacenPk, $articuloPk, $offset, $limit) {
        $this->db->where('fk_entidad', $fk_entidad);
        $this->db->where('fk_almacen', $almacenPk);
        $this->db->where('fk_articulo', $articuloPk);

        $this->db->order_by("fecha", "desc");

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $query = $this->db->get('historico_movimientos');

        $result = $query->result();

        return array("historico" => $result?$result:array());

    }


    /**
     * Funcion que devuelve los stocks de un almacen, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $alamcenPk
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(r_art_alm})
     *
     */
    function getMultipartCachedStocks($almacenPk, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $artsAlmacen = unserialize($this->esocialmemcache->get($key));
        } else {
            $this->db->where('fk_almacen', $almacenPk);
            $this->db->where('estado >=', $state);
            $this->db->where('updated_at >=', $lastTimeStamp);
            $query = $this->db->get('r_art_alm');

            $artsAlmacen = $query->result('r_art_alm');

            $rowcount = sizeof($artsAlmacen);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($artsAlmacen, $pagination->pageSize);

                $artsAlmacen = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "stocks" => $artsAlmacen?$artsAlmacen:array());

    }

    /**
     * Funcion que devuelve los stocks de los almacenes asociado a un usuraio, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $userPk
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(r_art_alm})
     *
     */
    function getMultipartCachedUserStocks($fk_entidad, $userPk, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $artsAlmacen = unserialize($this->esocialmemcache->get($key));
        } else {
            $q = "SELECT r_art_alm.* FROM r_art_alm
                    LEFT JOIN almacen_agente ON almacen_agente.fk_almacen = r_art_alm.fk_almacen AND almacen_agente.estado > 0 AND almacen_agente.fk_usuario_entidad = '".$userPk."'
                    LEFT JOIN (
						SELECT r_usu_emp.* FROM r_usu_emp
						JOIN tipo_agente ON r_usu_emp.fk_entidad = tipo_agente.fk_entidad AND fk_tipo_agente = pk_tipo_agente
						WHERE r_usu_emp.fk_entidad = ".$fk_entidad." AND r_usu_emp.pk_usuario_entidad = '".$userPk."' AND r_usu_emp.estado > 0 AND (cod_tipo_agente = 'AUTOVENTA' OR cod_tipo_agente = 'REPARTIDOR')
                    ) AS usuario ON usuario.fk_entidad = r_art_alm.fk_entidad AND usuario.fk_almacen_camion = r_art_alm.fk_almacen
                    WHERE r_art_alm.fk_entidad = ".$fk_entidad." AND r_art_alm.estado >= ".$state." AND (pk_usuario_entidad IS NOT NULL OR  almacen_agente.id IS NOT NULL)
                    AND (r_art_alm.updated_at >= '".$lastTimeStamp."' OR almacen_agente.updated_at >= '".$lastTimeStamp."' OR usuario.updated_at >= '".$lastTimeStamp."') ";

            $query = $this->db->query($q);

            $artsAlmacen = $query->result('r_art_alm');

            $rowcount = sizeof($artsAlmacen);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($artsAlmacen, $pagination->pageSize);

                $artsAlmacen = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "stocks" => $artsAlmacen?$artsAlmacen:array());

    }

    /**
     * Funcion que devuelve los stocks de los almacenes disponible para los tpv, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * @param $userPk
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(r_art_alm})
     *
     */
    function getMultipartCachedTpvStocks($fk_entidad, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $artsAlmacen = unserialize($this->esocialmemcache->get($key));
        } else {
            $q = "SELECT r_art_alm.* FROM r_art_alm
                    JOIN almacen ON almacen.fk_entidad = r_art_alm.fk_entidad AND pk_almacen = fk_almacen AND bool_disponible_tpv = 1
                    WHERE r_art_alm.fk_entidad = ".$fk_entidad." AND r_art_alm.estado >= ".$state." AND (r_art_alm.updated_at >= '".$lastTimeStamp."' OR almacen.updated_at >= '".$lastTimeStamp."')";

            $query = $this->db->query($q);

            $artsAlmacen = $query->result('r_art_alm');

            $rowcount = sizeof($artsAlmacen);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($artsAlmacen, $pagination->pageSize);

                $artsAlmacen = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "stocks" => $artsAlmacen?$artsAlmacen:array());

    }

    /**
     * Funcion que devuelve los alamcenes asociados a un usuraio, a partir de una fecha de actualizacion..
     * Las paginas generadas se cachean esperando las siguientes peticiones.
     *
     * IMPORTANTE: SI LA RELACION almacen_agente TIENE ESTADO = 0 DEVOLVEMOS EL ALAMACEN COMO ELIMINADO
     *
     * @param $userPk
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state
     * @param $lastTimeStamp
     * @return $pagination<br/> array(r_art_alm})
     *
     */
    function getMultipartCachedUserAlmacenes($userPk, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $almacenes = unserialize($this->esocialmemcache->get($key));
        } else {
            $q = "SELECT almacen.pk_almacen, almacen.fk_entidad, almacen.fk_provincia_entidad, almacen.cod_almacen, almacen.bool_principal, almacen.descripcion, almacen.direccion,
                    almacen.poblacion, almacen.codpostal, almacen.telefono_1, almacen.telefono_2, almacen.created_at, almacen.updated_at, almacen.token, almacen.fk_delegacion,
                    CASE
                        WHEN pk_usuario_entidad IS NOT NULL THEN 1
                        WHEN almacen_agente.estado = 0 THEN 0
                        ELSE almacen.estado
                    END AS estado
                    FROM almacen
                    LEFT JOIN almacen_agente ON almacen_agente.fk_almacen = almacen.pk_almacen AND almacen_agente.estado >= ".$state." AND almacen_agente.fk_usuario_entidad = '".$userPk."'
                    LEFT JOIN (
						SELECT r_usu_emp.* FROM r_usu_emp
						JOIN tipo_agente ON fk_tipo_agente = pk_tipo_agente
						WHERE r_usu_emp.pk_usuario_entidad = '".$userPk."' AND r_usu_emp.estado > 0 AND (cod_tipo_agente = 'AUTOVENTA' OR cod_tipo_agente = 'REPARTIDOR')
                    ) AS usuario ON usuario.fk_almacen_camion = almacen.pk_almacen
					WHERE almacen.estado >= ".$state." AND (pk_usuario_entidad IS NOT NULL OR  almacen_agente.id IS NOT NULL)
                    AND (almacen.updated_at >= '".$lastTimeStamp."' OR almacen_agente.updated_at >= '".$lastTimeStamp."' OR usuario.updated_at >= '".$lastTimeStamp."')";

            $query = $this->db->query($q);

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
     * Guarda la relacion entre articulo y almacen
     *
     * @param $artAlm
     * @return bool
     * @throws APIexception
     */
    function saveArticuloAlmacen($artAlm) {
        $this->load->model("log_model");

        if (!isset($artAlm->token)) {
            $artAlm->token = getToken();
        }

        $result = $artAlm->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on articulo_almacen_model->saveProduct. Unable to update r_art_alm.", ERROR_SAVING_DATA, serialize($artAlm));
        }
    }

    /**
     * Suma las unidades indicadas para el articulo y el almacen.
     * Si queremos restar enviaremos las unidades en negativo.
     *
     * @param $pkArticulo
     * @param $pkAlamcen
     * @param $unidades
     */
    //function addStockByArtAndAlmacen($pkArticulo, $pkAlmacen, $unidades) {
    function addStockByArtAndAlmacen($pkEntidad, $pkArticulo, $pkAlmacen, $tipo, $cantidad, $lote = null, $pkMovimiento = null, $fechaCaducidad = null, $fechaFabricacion = null, $pkAlmacenOrigen = null, $fechaCompra = null, $precioCompra = null, $ean = null ) {

        if (!$lote) $lote = 'NULL'; else $lote = "'$lote'";
        if (!$pkMovimiento) $pkMovimiento = 'NULL'; else $pkMovimiento = "'$pkMovimiento'";
        if (!$fechaCaducidad) $fechaCaducidad = 'NULL'; else $fechaCaducidad = "'$fechaCaducidad'";
        if (!$fechaFabricacion) $fechaFabricacion = 'NULL'; else $fechaFabricacion = "'$fechaFabricacion'";
        if (!$pkAlmacenOrigen) $pkAlmacenOrigen = 'NULL'; else $pkAlmacenOrigen = "'$pkAlmacenOrigen'";
        if (!$fechaCompra) $fechaCompra = 'NULL'; else $fechaCompra = "'$fechaCompra'";
        if (!$precioCompra) $precioCompra = 'NULL'; else $precioCompra = "'$precioCompra'";
        if (!$ean) $ean = 'NULL'; else $ean = "'$ean'";

        $result = $this->db->query( "CALL mueve_stock('$pkEntidad', '$pkAlmacen', '$pkArticulo', '$tipo', $cantidad, $lote, $pkMovimiento, $fechaCaducidad, $fechaFabricacion,  $pkAlmacenOrigen, $fechaCompra, $precioCompra, $ean, @result, @error_code, @error_descr)");
        return $result;

    }


    /**
     * Funcion que se encarga de guardar un inventario y sus lineas.
     * Para cada linea comprueba que no existe ya, buscando por el token.
     *
     * @param $inventario
     * @return bool
     * @throws APIexception
     */
    function saveInventario($inventario) {
        //$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
        $this->load->model("usuario_model");
        $this->load->model("log_model");

        if (!isset($inventario->token)) {
            $inventario->token = getToken();
        }

        $result = $inventario->_save(false, false);

        if ($result) {
            if (isset($inventario->inventarioLines)) {
                $inventarioLines = $inventario->inventarioLines;
                foreach ($inventarioLines as $line) {
                    $line->fk_inventario_cab = $inventario->pk_inventario_cab;
                    //Nos aseguramos que los Tokens no existen
                    if ($line->id_inventario_lin == null && isset($line->token)) {
                        $query = new stdClass();
                        $this->db->where('token', $line->token);
                        $query = $this->db->get("inventario_lin");
                        $inventarioLine = $query->row();
                        if ($inventarioLine) $line->id_inventario_lin = $inventarioLine->id_inventario_lin;
                    }
                    //Si nos viene con estado = 2 implica que esta consolidado y por lo tanto hay que modificar stocks.
                    if ($inventario->estado == 2) {

                        $cantidad = $line->cantidad_new - $line->cantidad_ant;
                        $res = $this->addStockByArtAndAlmacen($inventario->fk_entidad, $line->fk_articulo, $inventario->fk_almacen, "Inventario", $cantidad, $line->lote, $inventario->pk_inventario_cab);
                        if (!$res) throw new APIexception("Error on articulo_almacen_model->saveInventario. Unable to update UPDATE STOCKS", ERROR_SAVING_DATA, serialize($inventario));
                    }

                    if (!isset($line->token)) {
                        $line->token = getToken();
                    }
                    $res = $line->_save(false, true);
                    if (!$res) throw new APIexception("Error on articulo_almacen_model->saveInventario. Unable to update inventario Line", ERROR_SAVING_DATA, serialize($inventario));
                }
            }
            //$this->db->trans_complete();
            return true;
        } else {
            throw new APIexception("Error on articulo_almacen_model->saveInventario. Unable to update Inventario", ERROR_SAVING_DATA, serialize($inventario));
        }

    }

    /**
     * Funcion que se encarga de guardar un movimiento y sus lineas.
     * Para cada linea comprueba que no existe ya, buscando por el token.
     *
     * @param $movimiento
     * @return bool
     * @throws APIexception
     */
    function saveMovimiento($movimiento) {
        //$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
        $this->load->model("usuario_model");
        $this->load->model("log_model");

        if (!isset($movimiento->token)) {
            $movimiento->token = getToken();
        }

        $result = $movimiento->_save(false, false);

        if ($result) {
            if (isset($movimiento->movimientoLines)) {
                $movimientoLines = $movimiento->movimientoLines;
                foreach ($movimientoLines as $line) {
                    $line->pk_movimientos_almacen_cab = $movimiento->pk_movimientos_almacen_cab;
                    //Nos aseguramos que los Tokens no existen
                    if ($line->id_movimiento_lin == null && isset($line->token)) {
                        $query = new stdClass();
                        $this->db->where('token', $line->token);
                        $query = $this->db->get("movimientos_almacen_lin");
                        $movimientoLine = $query->row();
                        if ($movimientoLine) $line->id_movimiento_lin = $movimientoLine->id_movimiento_lin;
                    }
                    //Si nos viene con estado = 2 implica que esta consolidado y por lo tanto hay que modificar stocks.
                    if ($movimiento->estado == 2) {
                        //Descontammos del almacen origen
                        $res = $this->addStockByArtAndAlmacen($movimiento->fk_entidad, $line->fk_articulo, $movimiento->fk_almacen_ori, "Traspaso", $line->cantidad * (-1), $line->lote, $movimiento->pk_movimientos_almacen_cab);
                        if (!$res) throw new APIexception("Error on articulo_almacen_model->saveMovimiento. Unable to update UPDATE STOCKS", ERROR_SAVING_DATA, serialize($movimiento));
                        //Incrementamos el almacen destino
                        $res = $this->addStockByArtAndAlmacen($movimiento->fk_entidad, $line->fk_articulo, $movimiento->fk_almacen_des, "Traspaso", $line->cantidad, $line->lote, $movimiento->pk_movimientos_almacen_cab);
                        if (!$res) throw new APIexception("Error on articulo_almacen_model->saveMovimiento. Unable to update UPDATE STOCKS", ERROR_SAVING_DATA, serialize($movimiento));
                    }

                    if (!isset($line->token)) {
                        $line->token = getToken();
                    }
                    $res = $line->_save(false, true);
                    if (!$res) throw new APIexception("Error on articulo_almacen_model->saveMovimiento. Unable to update movimiento Line", ERROR_SAVING_DATA, serialize($movimiento));
                }
            }
            //$this->db->trans_complete();
            return true;
        } else {
            throw new APIexception("Error on articulo_almacen_model->saveMovimiento. Unable to update Movimiento", ERROR_SAVING_DATA, serialize($movimiento));
        }

    }

    /**
     * Funcion que devuelve los movimientos de un usuario a partir de una fecha.
     * El resultado se trocea y se cachea esperando las siguientes peticiones.
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination
     * @param $state --> 0 eliminado, 1 Activas, 2 Consolidadas. EN la primera actualizacion del tpv = 1 el resto >= 0
     * @param $lastTimeStamp
     * @return array|null
     */
    public function getMultipartCachedUserMovimientos($userPk, $entityId, $pagination, $state, $lastTimeStamp) {
        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $movimientos = unserialize($this->esocialmemcache->get($key));
        } else {


            $query = "SELECT pk_movimientos_almacen_cab, fk_entidad, fk_almacen_ori, fk_almacen_des, fk_usuario, cod_movimientos_alm, comentario, fecha, hora, cab.estado, cab.token,
                             id_movimiento_lin, fk_movimiento_cab, fk_articulo, cantidad, lin.estado AS estadoLin, lin.token AS tokenLin, cod_articulo
                    FROM movimientos_almacen_cab cab
                    JOIN movimientos_almacen_lin lin ON cab.pk_movimientos_almacen_cab = lin.fk_movimiento_cab AND lin.estado > 0
                    WHERE fk_entidad = '".$entityId."' AND fk_usuario = '".$userPk."' AND (lin.updated_at > '".$lastTimeStamp."' OR cab.updated_at > '".$lastTimeStamp."')";

            if ($state == 0) {
                //Enviamos todos
                $query .= " AND cab.estado >= 0";
            } else {
                //Enviamos los pendientes de consolidar
                $query .= " AND cab.estado = 1";
            }


            $query = $this->db->query($query);

            $movs = $query->result();

            $movimientos = array();
            $lins = array();
            $lastMov = "";
            $mov = null;
            for ($i=0; $i<count($movs); $i++) {
                if ($lastMov != $movs[$i]->pk_movimientos_almacen_cab) {
                    if ($mov) {
                        $mov->movimientoLines = $lins;
                        $movimientos[] = $mov;
                    }
                    $mov = new movimientoAlmacen();
                    $mov->set($movs[$i]);
                    $lins = array();

                    $lastMov = $mov->pk_movimientos_almacen_cab;
                }
                // Lineas
                $lin = new movimientoAlmacenLine();
                $lin->set($movs[$i]);
                //Asignamos los campos renombrados
                $lin->estado = $movs[$i]->estadoLin;
                $lin->token = $movs[$i]->tokenLin;
                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($mov) {
                $mov->movimientoLines = $lins;
                $movimientos[] = $mov;
            }

            $rowcount = sizeof($movimientos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($movimientos, $pagination->pageSize);

                $movimientos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "movimientos" => $movimientos?$movimientos:array());
    }

    /**
     * Funcion que devuelve los inventarios de un usuario a partir de una fecha.
     * El resultado se trocea y se cachea esperando las siguientes peticiones.
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination
     * @param $state --> 0 eliminado, 1 Activas, 2 Consolidadas. EN la primera actualizacion del tpv = 1 el resto >= 0
     * @param $lastTimeStamp
     * @return array|null
     */
    public function getMultipartCachedUserInventarios($userPk, $entityId, $pagination, $state, $lastTimeStamp) {
        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $inventarios = unserialize($this->esocialmemcache->get($key));
        } else {


            $query = "SELECT pk_inventario_cab, fk_entidad, fk_usuario, fk_almacen, cod_inventario, comentario, fecha, hora, cab.estado, cab.token,
                         id_inventario_lin, fk_inventario_cab, fk_articulo, cod_articulo, cantidad_ant, cantidad_new, lin.estado AS estadoLin, lin.token AS tokenLin
                    FROM inventario_cab cab
                    JOIN inventario_lin lin ON cab.pk_inventario_cab = lin.fk_inventario_cab AND lin.estado > 0
                    WHERE fk_entidad = '".$entityId."' AND fk_usuario = '".$userPk."' AND (lin.updated_at > '".$lastTimeStamp."' OR cab.updated_at > '".$lastTimeStamp."')";

            if ($state == 0) {
                //Enviamos todos
                $query .= " AND cab.estado >= 0";
            } else {
                //Enviamos los pendientes de consolidar
                $query .= " AND cab.estado = 1";
            }


            $query = $this->db->query($query);

            $invs = $query->result();

            $inventarios = array();
            $lins = array();
            $lastInv = "";
            $inv = null;
            for ($i=0; $i<count($invs); $i++) {
                if ($lastInv != $invs[$i]->pk_inventario_cab) {
                    if ($inv) {
                        $inv->inventarioLines = $lins;
                        $inventarios[] = $inv;
                    }
                    $inv = new inventario();
                    $inv->set($invs[$i]);
                    $lins = array();

                    $lastInv = $inv->pk_inventario_cab;
                }
                // Lineas
                $lin = new inventarioLine();
                $lin->set($invs[$i]);
                //Asignamos los campos renombrados
                $lin->estado = $invs[$i]->estadoLin;
                $lin->token = $invs[$i]->tokenLin;
                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($inv) {
                $inv->inventarioLines = $lins;
                $inventarios[] = $inv;
            }

            $rowcount = sizeof($inventarios);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($inventarios, $pagination->pageSize);

                $inventarios = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "inventarios" => $inventarios?$inventarios:array());
    }

    /**
     * Funcion que devuelve un movimiento a partir de su Token
     *
     * @param $token
     * @param $entityId
     * @return movimientoAlmacen
     */
    function getMovimientoByToken($token, $entityId) {

        //CABECERA
        $this->db->where('token', $token);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('movimientos_almacen_cab');

        $movimiento = $query->row(0, 'movimientoAlmacen');

        //LINEAS
        if ($movimiento) {
            $this->db->where('fk_movimiento_cab', $movimiento->pk_movimientos_almacen_cab);
            $query = $this->db->get('movimientos_almacen_lin');
            $movimientoLines = $query->result('movimientoAlmacenLine');

            $movimiento->movimientoLines = $movimientoLines;
        }

        return $movimiento;

    }

    /**
     * Funcion que devuelve un inventario a partir de su Token
     *
     * @param $token
     * @param $entityId
     * @return inventario
     */
    function getInventarioByToken($token, $entityId) {

        //CABECERA
        $this->db->where('token', $token);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('inventario_cab');

        $inventario = $query->row(0, 'inventario');

        //LINEAS
        if ($inventario) {
            $this->db->where('fk_inventario_cab', $inventario->pk_inventario_cab);
            $query = $this->db->get('inventario_lin');
            $inventarioLines = $query->result('inventarioLine');

            $inventario->movimientoLines = $inventarioLines;
        }

        return $inventario;

    }

    /**
     * Funcion que se encarga de actualizar todos los costes medios de aprtir de los costes de existencias
     */
    function updateCosteMedio() {
        //Articulos NO compuestos
        $sql = "UPDATE r_art_alm r
                JOIN (
                    SELECT exi.fk_entidad, fk_almacen, fk_articulo, SUM(existencias*precio_compra)/SUM(existencias) AS coste_medio
                    FROM existencias_compras exi
                    JOIN articulos art ON art.fk_entidad = exi.fk_entidad AND art.pk_articulo = exi.fk_articulo
                    WHERE existencias > 0 AND bool_articulo_compuesto = 0
                    GROUP BY fk_entidad, fk_almacen, fk_articulo
                ) e ON r.fk_entidad = e.fk_entidad AND r.fk_almacen = e.fk_almacen AND r.fk_articulo = e.fk_articulo
                SET r.coste_medio = e.coste_medio"."";
        $this->db->query($sql);
        //Articulo compuesto.
        $sql = "UPDATE r_art_alm r
                JOIN (
                    SELECT com.fk_entidad, fk_almacen, com.fk_articulo, SUM(cantidad*IFNULL(coste_medio,0)) AS coste_medio
                    FROM r_art_composicion com
                    JOIN articulos art ON art.fk_entidad = com.fk_entidad AND art.pk_articulo = com.fk_articulo
                    JOIN  r_art_alm ralm ON ralm.fk_entidad = com.fk_entidad AND com.fk_articulo_compuesto = ralm.fk_articulo
                    WHERE cantidad > 0 AND bool_articulo_compuesto = 1
                    GROUP BY fk_entidad, fk_almacen, fk_articulo
                ) e ON r.fk_entidad = e.fk_entidad AND r.fk_almacen = e.fk_almacen AND r.fk_articulo = e.fk_articulo
                SET r.coste_medio = e.coste_medio"."";
        $this->db->query($sql);
    }



}

?>