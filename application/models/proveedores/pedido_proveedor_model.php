<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_ALMACEN);


class pedido_proveedor_model extends CI_Model {
    
    /**
     * @param $proveedorPk
     * @return the pedido
     */
    function getPedidoProveedorByPk($pedidoProveedorPk) {
    	$this->db->where('pk_pedido_proveedor_cab', $pedidoProveedorPk);
    	$query = $this->db->get('pedido_proveedor_cab');
    
    	$pedido = $query->row(0, 'pedidoProveedorCab');
    	return $pedido;
    }
    
    /**
     * @param $token
     * @return the pedido
     */
    function getPedidoProveedorByToken($token) {
    	//Cabecera
    	$this->db->where('token', $token);
    	$query = $this->db->get('pedido_proveedor_cab');
    
    	$pedido = $query->row(0, 'pedidoProveedorCab');
    	
    	//Lineas
    	if ($pedido) {
    		$this->db->where('fk_pedido_proveedor_cab', $pedido->pk_pedido_proveedor_cab);
    		$query = $this->db->get('pedido_proveedor_lin');
    		$pedidoLines = $query->result('pedidoProveedorLin');
    	
    		$pedido->pedidoLines = $pedidoLines;
    	}
    	
    	return $pedido;
    }

    /**
     * Devuelve los pedidos de un almacen
     *
     * @param $entityId
     * @param $state
     * @param $almacenPk
     * @param $proveedor (Opcional)
     * @param $fecha (Opcional)
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     *
     * @return array
     */
    function getAll($entityId, $state, $almacenPk, $usuarioPk, $fecha, $proveedor, $offset, $limit) {
        $query = "SELECT cab.*, pro.raz_social FROM pedido_proveedor_cab cab
                    JOIN proveedores pro ON cab.fk_entidad = pro.fk_entidad AND fk_proveedor = pk_proveedor
                    WHERE cab.fk_entidad = $entityId AND fk_almacen = '$almacenPk' AND cab.estado > 0 ";

        if ($state)
            $query .= " AND cab.estado = $state ";

        if ($fecha)
            $query .= " AND cab.fecha = '$fecha' ";

        if ($usuarioPk)
            $query .= " AND cab.fk_usuario = '$usuarioPk' ";

        if ($proveedor)
            $query .= " AND (pro.nombre_comercial LIKE '%$proveedor%' OR pro.raz_social LIKE '%$proveedor%') ";

        $query .= " ORDER BY cab.fecha DESC, cab.hora DESC";

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $query .= " LIMIT " . $limit . " OFFSET " . $offset;


        $query = $this->db->query($query);

        $result = $query->result('pedidoProveedorCab');

        return array("pedidos" => $result?$result:array());

    }


    /**
     * Devuelve las lineas de un pedido de proveedor
     *
     * @param $pedidoPk
     *
     * @return array
     */
    function getLines($entityId, $pedidoPk) {

        $query = "SELECT lin.*, art.descripcion, art.bool_control_lote, lote.*, lin.estado AS estado_lin, lin.token AS token_lin, lote.token AS token_lote, lote.estado AS estado_lote
                    FROM pedido_proveedor_lin lin
                    JOIN articulos art ON art.fk_entidad = $entityId AND fk_articulo = pk_articulo
                    LEFT JOIN pedido_proveedor_lin_lote lote ON lote.fk_pedido_proveedor_lin = lin.id_pedido_proveedor_lin
                    WHERE fk_pedido_proveedor_cab = '$pedidoPk'";

        $query = $this->db->query($query);

        $result = $query->result('pedidoProveedorLin');

        $lastLine = "";
        $line = null;
        $lotes = array();
        $lines = array();
        for ($i=0; $i<count($result); $i++) {
            if ($lastLine != $result[$i]->id_pedido_proveedor_lin) {
                if ($line) {
                    $line->lotes = $lotes;
                    $lines[] = $line;
                }
                $line = new pedidoProveedorLin();
                $line->set($result[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $line->estado = $result[$i]->estado_lin;
                $line->token = $result[$i]->token_lin;
                $line->descripcion = $result[$i]->descripcion;
                $line->bool_control_lote = $result[$i]->bool_control_lote;
                $lotes = array();

                $lastLine = $line->id_pedido_proveedor_lin;
            }
            // Linea de Pedido
            $lote = new pedidoProveedorLinLote();
            $lote->set($result[$i]);
            //Asignamos los campos renombrados
            $lote->estado = $result[$i]->estado_lote;
            $lote->token = $result[$i]->token_lote;

            if ($lote->lote)
                $lotes[] = $lote;

        }
        //Metemos el ultimo
        if ($line) {
            $line->lotes = $lotes;
            $lines[] = $line;
        }


        return array("lines" => $lines?$lines:array());

    }

    /**
     * Devuelve el pedido sugerido para un almacen y un proveedor.
     *
     * @param $entityId
     * @param $almacenPk
     * @param $proveedorPk
     *
     * @return array
     */
    function getPedidoSugerido($entityId, $almacenPk, $proveedorPk) {
        $query = "SELECT ralm.fk_articulo, cod_articulo, art.descripcion, (stock_max - unidades) AS unidades_solicitadas, (stock_max - unidades) AS unidades_entregadas, precio_coste, precio_coste AS precio, rpro.iva, cod_art_prov
                   FROM  articulos art
                   JOIN r_art_alm ralm ON ralm.fk_entidad = art.fk_entidad AND pk_articulo = ralm.fk_articulo AND fk_almacen = '$almacenPk' AND ralm.estado > 0
                   JOIN r_art_pro rpro ON rpro.fk_entidad = art.fk_entidad AND pk_articulo = rpro.fk_articulo AND fk_proveedor = '$proveedorPk' AND rpro.estado > 0
                   WHERE art.fk_entidad = $entityId AND unidades < stock_min AND art.estado > 0 AND (art.fecha_baja IS NULL OR art.fecha_baja > NOW()) ";


        $query = $this->db->query($query);

        $result = $query->result();

        return array("sugerido" => $result?$result:array());

    }
    
    /**
     * Guarda/actualiza el pedido al proveedor en la base de datos
     *
     * @param $proveedor
     * @return bool
     * @throws APIexception
     */
    function savePedidoProveedor($pedido) {
    	$this->load->model("log_model");
    
    	if (!isset($pedido->token)) {
    		$pedido->token = getToken();
    	}
    
    	$result = $pedido->_save(false, false);
    	
    	if ($result) {
    		if (isset($pedido->pedidoLines)) {
    			$pedidoLines = $pedido->pedidoLines;
    			foreach ($pedidoLines as $line) {
    				$line->fk_pedido_proveedor_cab = $pedido->pk_pedido_proveedor_cab;
    				//Nos aseguramos que los Tokens no existen
    				if ($line->id_pedido_proveedor_lin == null && isset($line->token)) {
    					$query = new stdClass();
    					$this->db->where('token', $line->token);
    					$query = $this->db->get("pedido_proveedor_lin");
    					$pedidoLine = $query->row();
    					if ($pedidoLine) $line->id_pedido_proveedor_lin = $pedidoLine->id_pedido_proveedor_lin;
    				}
    				if (!isset($line->token)) {
    					$line->token = getToken();
    				}
    				$res = $line->_save(false, true);
    				if (!$res) throw new APIexception("Error on pedido_proveedor_model->savePedidoProveedor. Unable to update Pedido Line", ERROR_SAVING_DATA, serialize($pedido));
                    if ($line->id_pedido_proveedor_lin == null)
                        $line->id_pedido_proveedor_lin = $res;

                    //Guardamos los lotes
                    if (isset($line->lotes)) {
                        foreach ($line->lotes as $lote) {
                            if ($lote->pk_lote == null && isset($lote->token)) {
                                $query = new stdClass();
                                $this->db->where('token', $lote->token);
                                $query = $this->db->get("pedido_proveedor_lin_lote");
                                $loteLine = $query->row();
                                if ($loteLine) {
                                    $lote->pk_lote = $loteLine->pk_lote;
                                    $lote->fk_pedido_proveedor_lin = $loteLine->fk_pedido_proveedor_lin;
                                }
                            }
                            if ($lote->fk_pedido_proveedor_lin == null)
                                $lote->fk_pedido_proveedor_lin = $line->id_pedido_proveedor_lin;

                            $res = $lote->_save(false, true);
                            if (!$res) throw new APIexception("Error on pedido_proveedor_model->savePedidoProveedor. Unable to update Pedido Line Lote", ERROR_SAVING_DATA, serialize($pedido));
                        }
                    }


                    //Si la linea no existia y viene con estado 2, hay que hacer el moviento en el almacen.
                    if ($line->estado == 2 && !$line->id_pedido_proveedor_lin) {
                        $this->load->model('articulos/articulo_almacen_model');

                        if (!$line->lotes) {
                            $cantidad = $line->unidades_entregadas;
                            $res = $this->articulo_almacen_model->addStockByArtAndAlmacen($pedido->fk_entidad, $line->fk_articulo, $pedido->fk_almacen, "Pedido", $cantidad, null, $pedido->pk_pedido_proveedor_cab, null, null, null, $pedido->fecha, $line->precio, null);
                            if (!$res) throw new APIexception("Error on pedido_proveedor_model->savePedido. Unable to update UPDATE STOCKS", ERROR_SAVING_DATA, serialize($pedido));

                        } else {
                            foreach ($line->lotes as $lote) {
                                $cantidad = $lote->stock;
                                $res = $this->articulo_almacen_model->addStockByArtAndAlmacen($pedido->fk_entidad, $line->fk_articulo, $pedido->fk_almacen, "Pedido", $cantidad, $lote->lote, $pedido->pk_pedido_proveedor_cab, $lote->fecha_caducidad, $lote->fecha_fabricacion, null, $pedido->fecha, $line->precio, $lote->codigo_ean);
                                if (!$res) throw new APIexception("Error on pedido_proveedor_model->savePedido. Unable to update UPDATE STOCKS", ERROR_SAVING_DATA, serialize($pedido));
                            }
                        }
                    }

    			}
    		}
    		return true;
    	} else {
    		throw new APIexception("Error on pedido_proveedor_model->savePedidoProveedor. Unable to update proveedor.", ERROR_SAVING_DATA, serialize($pedido));
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
            $return = 'pk_pedido_proveedor_cab';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('pedido_proveedor_cab');
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
     * @param $entityPk
     *
     * Coge el siguiente numero de pedido y actualiza la tabla
     */
    function getCode($entityPk) {

        $this->db->trans_start();

        $this->db->where('fk_entidad', $entityPk);
        $query = $this->db->get('code');

        $result = $r_usu_cli = $query->row(0);

        if ($result) {
            $q = new stdClass();
            $q->last_pedido_proveedor = $result->last_pedido_proveedor + 1;
            $this->db->where('fk_entidad', $entityPk);

            $this->db->update('code', $q);

            $this->db->trans_complete();

            return str_pad($result->last_pedido_proveedor + 1, 7, "0", STR_PAD_LEFT);

        } else {
            return null;
        }

    }


}

?>