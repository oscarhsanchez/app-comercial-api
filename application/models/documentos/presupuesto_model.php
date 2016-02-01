<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_BUDGET);
require_once(APPPATH.ENTITY_BUDGET_LINE);
require_once(APPPATH.ENTITY_EXCEPTION);

class presupuesto_model extends CI_Model {

    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $fromDate) {
        $q = " SELECT 	cab.pk_presu_cab, cab.fk_entidad, cab.fk_usuario, cab.serie, cab.anio, cab.fk_serie_entidad, cab.fk_cliente, cab.fk_delegacion, cab.fk_terminal_tpv, cab.fk_forma_pago, cab.fk_condicion_pago, cab.cod_presupuesto,
                cab.cod_usuario_entidad, cab.num_serie, cab.cod_cliente, cab.cod_delegacion, cab.cod_terminal_tpv, cab.cod_forma_pago, cab.cod_condicion_pago, cab.bool_actualiza_numeracion, cab.bool_recalcular, cab.fecha,
                cab.raz_social, cab.nif, cab.direccion, cab.poblacion, cab.provincia, cab.codpostal, cab.base_imponible_tot, cab.imp_desc_tot, cab.imp_promo_lin_total, cab.imp_iva_tot, cab.imp_re_tot, cab.imp_total, cab.observaciones, cab.varios1,
                cab.varios2, cab.varios3, cab.varios4, cab.varios5, cab.varios6, cab.varios7, cab.varios8, cab.varios9, cab.varios10, cab.estado AS estadoPed, cab.token AS tokenPed, cab.fk_pedido_destino, cab.fk_albaran_destino, cab.fk_factura_destino, cab.bool_impreso,

                lin.id_presu_lin, lin.fk_presu_cab, lin.fk_usuario AS fk_usuarioLin, lin.cod_usuario_entidad, lin.cod_concepto, lin.concepto, lin.cantidad, lin.precio, lin.base_imponible, lin.descuento, lin.imp_descuento, lin.iva, lin.imp_iva, desc_promocion, imp_promocion,
                lin.re, lin.imp_re, lin.total_lin, lin.varios1 AS varios1Lin, lin.varios2 AS varios2Lin, lin.varios3 AS varios3Lin, lin.varios4 AS varios4Lin, lin.varios5 AS varios5Lin, lin.varios6 AS varios6Lin, lin.varios7 AS varios7Lin,
                lin.varios8 AS varios8Lin, lin.varios9 AS varios9Lin, lin.varios10 AS varios10Lin, lin.fk_promocion, lin.estado as estadoLin,
                lin.token AS tokenLin, lin.fk_articulo, lin.fk_tarifa, lin.precio_original, lin.fk_entidad, lin.precio_punto_verde, lin.coste_medio, lin.bool_precio_neto

                FROM presupuestos_cab cab
                JOIn presupuestos_lin lin on lin.fk_entidad = cab.fk_entidad and pk_presu_cab = fk_presu_cab
                JOIN r_usu_cli ON r_usu_cli.fk_entidad = cab.fk_entidad AND cab.fk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= 0
                WHERE cab.fk_entidad = ".$entityId." AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_at > '".$lastTimeStamp."' OR cab.updated_at > '".$lastTimeStamp."' OR lin.updated_at > '".$lastTimeStamp."')
                and cab.fecha  >= '".$fromDate."' AND cab.estado > ".$state."
                union
                SELECT 	cab.pk_presu_cab, cab.fk_entidad, cab.fk_usuario, cab.serie, cab.anio, cab.fk_serie_entidad, cab.fk_cliente, cab.fk_delegacion, cab.fk_terminal_tpv, cab.fk_forma_pago, cab.fk_condicion_pago, cab.cod_presupuesto,
                cab.cod_usuario_entidad, cab.num_serie, cab.cod_cliente, cab.cod_delegacion, cab.cod_terminal_tpv, cab.cod_forma_pago, cab.cod_condicion_pago, cab.bool_actualiza_numeracion, cab.bool_recalcular, cab.fecha,
                cab.raz_social, cab.nif, cab.direccion, cab.poblacion, cab.provincia, cab.codpostal, cab.base_imponible_tot, cab.imp_desc_tot, cab.imp_promo_lin_total, cab.imp_iva_tot, cab.imp_re_tot, cab.imp_total, cab.observaciones, cab.varios1,
                cab.varios2, cab.varios3, cab.varios4, cab.varios5, cab.varios6, cab.varios7, cab.varios8, cab.varios9, cab.varios10, cab.estado AS estadoPed, cab.token AS tokenPed, cab.fk_pedido_destino, cab.fk_albaran_destino, cab.fk_factura_destino, cab.bool_impreso,

                lin.id_presu_lin, lin.fk_presu_cab, lin.fk_usuario AS fk_usuarioLin, lin.cod_usuario_entidad, lin.cod_concepto, lin.concepto, lin.cantidad, lin.precio, lin.base_imponible, lin.descuento, lin.imp_descuento, lin.iva, lin.imp_iva, desc_promocion, imp_promocion,
                lin.re, lin.imp_re, lin.total_lin, lin.varios1 AS varios1Lin, lin.varios2 AS varios2Lin, lin.varios3 AS varios3Lin, lin.varios4 AS varios4Lin, lin.varios5 AS varios5Lin, lin.varios6 AS varios6Lin, lin.varios7 AS varios7Lin,
                lin.varios8 AS varios8Lin, lin.varios9 AS varios9Lin, lin.varios10 AS varios10Lin, lin.fk_promocion, lin.estado AS estadoLin,
                lin.token AS tokenLin, lin.fk_articulo, lin.fk_tarifa, lin.precio_original, lin.fk_entidad, lin.precio_punto_verde, lin.coste_medio, lin.bool_precio_neto

                FROM presupuestos_cab cab
                JOIN presupuestos_lin lin ON lin.fk_entidad = cab.fk_entidad AND pk_presu_cab = fk_presu_cab
                JOIN r_usu_cap on cab.fk_entidad = r_usu_cap.fk_entidad and cab.fk_cliente = r_usu_cap.fk_cliente AND fk_usuario_vendedor = '".$userPk."' AND r_usu_cap.estado >= 0
                WHERE cab.fk_entidad = ".$entityId." AND (r_usu_cap.updated_at > '".$lastTimeStamp."' OR cab.updated_at > '".$lastTimeStamp."' OR lin.updated_at > '".$lastTimeStamp."') and cab.fecha  >= '".$fromDate."' AND cab.estado > ".$state;


        return $q;
    }

    private function getActivoQuery($entityId, $clientId, $lastTimeStamp) {
        $q = " SELECT 	cab.pk_presu_cab, cab.fk_entidad, cab.fk_usuario, cab.serie, cab.anio, cab.fk_serie_entidad, cab.fk_cliente, cab.fk_delegacion, cab.fk_terminal_tpv, cab.fk_forma_pago, cab.fk_condicion_pago, cab.cod_presupuesto,
                cab.cod_usuario_entidad, cab.num_serie, cab.cod_cliente, cab.cod_delegacion, cab.cod_terminal_tpv, cab.cod_forma_pago, cab.cod_condicion_pago, cab.bool_actualiza_numeracion, cab.bool_recalcular, cab.fecha,
                cab.raz_social, cab.nif, cab.direccion, cab.poblacion, cab.provincia, cab.codpostal, cab.base_imponible_tot, cab.imp_desc_tot, cab.imp_iva_tot, cab.imp_re_tot, cab.imp_total, cab.imp_promo_lin_total, cab.observaciones, cab.varios1,
                cab.varios2, cab.varios3, cab.varios4, cab.varios5, cab.varios6, cab.varios7, cab.varios8, cab.varios9, cab.varios10, cab.estado AS estadoPed, cab.token AS tokenPed, cab.fk_pedido_destino, cab.fk_albaran_destino, cab.fk_factura_destino, cab.bool_impreso,

                lin.id_presu_lin, lin.fk_presu_cab, lin.fk_usuario AS fk_usuarioLin, lin.cod_usuario_entidad, lin.cod_concepto, lin.concepto, lin.cantidad, lin.precio, lin.base_imponible, lin.descuento, lin.imp_descuento, lin.iva, lin.imp_iva, desc_promocion, imp_promocion,
                lin.re, lin.imp_re, lin.total_lin, lin.varios1 AS varios1Lin, lin.varios2 AS varios2Lin, lin.varios3 AS varios3Lin, lin.varios4 AS varios4Lin, lin.varios5 AS varios5Lin, lin.varios6 AS varios6Lin, lin.varios7 AS varios7Lin,
                lin.varios8 AS varios8Lin, lin.varios9 AS varios9Lin, lin.varios10 AS varios10Lin, lin.fk_promocion, lin.estado as estadoLin,
                lin.token AS tokenLin, lin.fk_articulo, lin.fk_tarifa, lin.precio_original, lin.fk_entidad, lin.precio_punto_verde, lin.coste_medio, lin.bool_precio_neto

                FROM presupuestos_cab cab
                JOIN presupuestos_lin lin on lin.fk_entidad = cab.fk_entidad and pk_presu_cab = fk_presu_cab
                WHERE cab.fk_entidad = ".$entityId." AND fk_cliente = '".$clientId."' AND (cab.updated_at > '".$lastTimeStamp."' OR lin.updated_at > '".$lastTimeStamp."')
                AND cab.estado > 0 AND bool_presu_activo > 0";


        return $q;
    }

    private function getByClientQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {
        $q = " SELECT 	cab.pk_presu_cab, cab.fk_entidad, cab.fk_usuario, cab.serie, cab.anio, cab.fk_serie_entidad, cab.fk_cliente, cab.fk_delegacion, cab.fk_terminal_tpv, cab.fk_forma_pago, cab.fk_condicion_pago, cab.cod_presupuesto,
                cab.cod_usuario_entidad, cab.num_serie, cab.cod_cliente, cab.cod_delegacion, cab.cod_terminal_tpv, cab.cod_forma_pago, cab.cod_condicion_pago, cab.bool_actualiza_numeracion, cab.bool_recalcular, cab.fecha,
                cab.raz_social, cab.nif, cab.direccion, cab.poblacion, cab.provincia, cab.codpostal, cab.base_imponible_tot, cab.imp_desc_tot, cab.imp_iva_tot, cab.imp_re_tot, cab.imp_total, cab.imp_promo_lin_total, cab.observaciones, cab.varios1,
                cab.varios2, cab.varios3, cab.varios4, cab.varios5, cab.varios6, cab.varios7, cab.varios8, cab.varios9, cab.varios10, cab.estado AS estadoPresu, cab.token AS tokenPresu, cab.fk_pedido_destino, cab.fk_albaran_destino, cab.fk_factura_destino, cab.bool_impreso,

                lin.id_presu_lin, lin.fk_presu_cab, lin.fk_usuario AS fk_usuarioLin, lin.cod_usuario_entidad, lin.cod_concepto, lin.concepto, lin.cantidad, lin.precio, lin.base_imponible, lin.descuento, lin.imp_descuento, lin.iva, lin.imp_iva, desc_promocion, imp_promocion,
                lin.re, lin.imp_re, lin.total_lin, lin.varios1 AS varios1Lin, lin.varios2 AS varios2Lin, lin.varios3 AS varios3Lin, lin.varios4 AS varios4Lin, lin.varios5 AS varios5Lin, lin.varios6 AS varios6Lin, lin.varios7 AS varios7Lin,
                lin.varios8 AS varios8Lin, lin.varios9 AS varios9Lin, lin.varios10 AS varios10Lin, lin.fk_promocion, lin.estado as estadoLin,
                lin.token AS tokenLin, lin.fk_articulo, lin.fk_tarifa, lin.precio_original, lin.fk_entidad, lin.precio_punto_verde, lin.coste_medio, lin.bool_precio_neto

                FROM presupuestos_cab cab
                JOIN presupuestos_lin lin on lin.fk_entidad = cab.fk_entidad and pk_presu_cab = fk_presu_cab and lin.estado >= $state
                WHERE cab.fk_entidad = ".$entityId." AND fk_cliente = '".$clientePk."' AND (cab.updated_at > '".$lastTimeStamp."' OR lin.updated_at > '".$lastTimeStamp."')
                and cab.fecha >= '$fromDate'
                AND cab.estado >= $state";


        return $q;
    }

	private function getBudgetQuery() {
		$this->db->select('presupuestos_cab.*', false);
		$this->db->from('presupuestos_cab');
	}

    private function getListQuery($entityId, $clientePk, $offset, $limit, $order, $sort) {
        $q = "SELECT * FROM presupuestos_cab WHERE estado > 0 AND fk_entidad = ".$entityId;

        if ($clientePk) $q .= " AND fk_cliente = '".$clientePk."'";
        if ($order) {
            $q .= " ORDER BY ".$order;
            if ($sort == "DESC") $q .= " DESC ";
            else $q .= " ASC ";
        }

        if ($limit) {
            $q .= " LIMIT ".$limit;
            if ($offset) $q .= " OFFSET ".$offset;
        }

        return $q;
    }

    /**
     * Funcion que devuelve un listado de presupuestos.
     *
     * @param $entityId
     * @param $clientePk (opcional)
     * @param $offset (opcional)
     * @param $limit (opcional)
     * @param $order (opcional)
     *
     */
    function listPresupuestos($entityId, $clientePk, $offset, $limit, $order, $sort) {
        $query = $this->getListQuery($entityId, $clientePk, $offset, $limit, $order, $sort);

        $query = $this->db->query($query);

        $presupuestos = $query->result('presupuesto');

        return array("presupuestos" => $presupuestos?$presupuestos:array());

    }


    function getBudgetByPk($budgetPk) {

		//CABECERA
		$this->db->where('presupuestos_cab.pk_presu_cab', $budgetPk);
		$this->getBudgetQuery();		
		$query = $this->db->get();
		
		$budget = $query->row(0, 'presupuesto');

		//LINEAS
		if ($budget) {
			$this->db->where('fk_presu_cab', $budgetPk);
			$query = $this->db->get('presupuestos_lin');
			$budgetLines = $query->result('presuLine');

			$budget->budgetLines = $budgetLines;
		}

		return $budget;

	}

	function getBudgetByCod($budgetCod, $entityId) {

		//CABECERA
		$this->db->where('presupuestos_cab.cod_presupuesto', $budgetCod);
		$this->db->where('presupuestos_cab.fk_entidad', $entityId);		
		$this->getBudgetQuery();		
		$query = $this->db->get();
		
		$budget = $query->row(0, 'presupuesto');

		//LINEAS
		if ($budget) {
			$this->db->where('fk_presu_cab', $budgetPk);
			$query = $this->db->get('presupuestos_lin');
			$budgetLines = $query->result('presuLine');

			$budget->budgetLines = $budgetLines;
		}

		return $budget;

	}


	function getBudgetByToken($token, $entityId) {

		//CABECERA
		$this->db->where('presupuestos_cab.token', $token);
		$this->db->where('presupuestos_cab.fk_entidad', $entityId);
        $query = $this->db->get('presupuestos_cab');
		$budget = $query->row(0, 'presupuesto');

		//LINEAS
		if ($budget) {
			$this->db->where('fk_presu_cab', $budget->pk_presu_cab);
			$query = $this->db->get('presupuestos_lin');
			$budgetLines = $query->result('presuLine');

			$budget->budgetLines = $budgetLines;
		}

		return $budget;

	}

    /**
     * Funcion que devuelve los presupuesto de un cliente
     *
     * @param $entityId
     * @param $clientePk
     * @param $fromDate
     * @param $lastTimeStamp
     * @param $state
     * @return array(presupuestos)
     *
     */
    function getByCliente($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        $this->load->library('esocialmemcache');


        $query = $this->getByClientQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state);

        $query = $this->db->query($query);

        $result = $query->result();

        $presus = array();
        $lins = array();
        $lastPresu = "";
        $presu = null;
        for ($i=0; $i<count($result); $i++) {
            if ($lastPresu != $result[$i]->pk_presu_cab) {
                if ($presu) {
                    $presu->presupuestoLines = $lins;
                    $presus[] = $presu;
                }
                $presu = new presupuesto();
                $presu->set($result[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $presu->estado = $result[$i]->estadoPresu;
                $presu->token = $result[$i]->tokenPresu;
                $lins = array();

                $lastPresu = $presu->pk_presu_cab;
            }
            // Linea de Presu
            $lin = new presuLine();
            $lin->set($result[$i]);
            //Asignamos los campos renombrados
            $lin->fk_usuario = $result[$i]->fk_usuarioLin;
            $lin->estado = $result[$i]->estadoLin;
            $lin->token = $result[$i]->tokenLin;
            $lin->varios1 = $result[$i]->varios1Lin;
            $lin->varios2 = $result[$i]->varios2Lin;
            $lin->varios3 = $result[$i]->varios3Lin;
            $lin->varios4 = $result[$i]->varios4Lin;
            $lin->varios5 = $result[$i]->varios5Lin;
            $lin->varios6 = $result[$i]->varios6Lin;
            $lin->varios7 = $result[$i]->varios7Lin;
            $lin->varios8 = $result[$i]->varios8Lin;
            $lin->varios9 = $result[$i]->varios9Lin;
            $lin->varios10 = $result[$i]->varios10Lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($presu) {
            $presu->presupuestoLines = $lins;
            $presus[] = $presu;
        }


        return array("presupuestos" => $presus?$presus:array());

    }

    /**
     * Funcion que devuelve el presupuesto activo de un cliente a partir de una fecha de actualizacion.
     *
     * @param $entityId
     * @param $clientId
     * @param $lastTimeStamp
     * @return null|presupuesto
     */
    function getPresuActivo($entityId, $clientId, $lastTimeStamp) {
        $query = $this->getActivoQuery($entityId, $clientId, $lastTimeStamp);

        $query = $this->db->query($query);
        $result = $query->result();

        $presu = null;
        if (count($result) > 0) {
            $presu = new presupuesto();
            $presu->set($result[0]);
            //Asignamos el token y estado que lo hemos renombrado en la consulta
            $presu->estado = $result[0]->estadoPed;
            $presu->token = $result[0]->tokenPed;
            $lins = array();

            for ($i=0; $i<count($result); $i++) {

                // Linea de Presupuesto
                $lin = new presuLine();
                $lin->set($result[$i]);
                //Asignamos los campos renombrados
                $lin->fk_usuario = $result[$i]->fk_usuarioLin;
                $lin->estado = $result[$i]->estadoLin;
                $lin->token = $result[$i]->tokenLin;
                $lin->varios1 = $result[$i]->varios1Lin;
                $lin->varios2 = $result[$i]->varios2Lin;
                $lin->varios3 = $result[$i]->varios3Lin;
                $lin->varios4 = $result[$i]->varios4Lin;
                $lin->varios5 = $result[$i]->varios5Lin;
                $lin->varios6 = $result[$i]->varios6Lin;
                $lin->varios7 = $result[$i]->varios7Lin;
                $lin->varios8 = $result[$i]->varios8Lin;
                $lin->varios9 = $result[$i]->varios9Lin;
                $lin->varios10 = $result[$i]->varios10Lin;

                $lins[] = $lin;

            }

            $presu->presupuestoLines = $lins;

        }

        return $presu;

    }

    /**
     * Marca todos los presupuestos de un cliente que esten activos como NO activo.
     * Solo deberia haber un presupuesto activo.
     *
     * @param $entityId
     * @param $clientId
     * @return mixed
     */
    function setPresusNoActivos($entityId, $clientId) {
        $q = new stdClass();
        $q->bool_presu_activo = 0;
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('fk_cliente', $clientId);

        return $this->db->update('presupuestos_cab', $q);
    }


    /**
     * Funcion que devuelve los presupuestos de los clientes asignados a un usuario (Vendedor o repartidor)
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los presupuestos a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $fromDate
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(prespuestos)
     *
     */
    function getMultipartCachedClientesPresupuestos($userPk, $entityId, $pagination, $state, $lastTimeStamp, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $presus = unserialize($this->esocialmemcache->get($key));
            if (!$presus) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $fromDate);

            $query = $this->db->query($query);

            $presusdb = $query->result();

            $presus = array();
            $lins = array();
            $lastPresu = "";
            $presu = null;
            for ($i=0; $i<count($presusdb); $i++) {
                if ($lastPresu != $presusdb[$i]->pk_presu_cab) {
                    if ($presu) {
                        $presu->presupuestoLines = $lins;
                        $presus[] = $presu;
                    }
                    $presu = new presupuesto();
                    $presu->set($presusdb[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $presu->estado = $presusdb[$i]->estadoPed;
                    $presu->token = $presusdb[$i]->tokenPed;
                    $lins = array();

                    $lastPresu = $presu->pk_presu_cab;
                }
                // Linea de Presupuesto
                $lin = new presuLine();
                $lin->set($presusdb[$i]);
                //Asignamos los campos renombrados
                $lin->fk_usuario = $presusdb[$i]->fk_usuarioLin;
                $lin->estado = $presusdb[$i]->estadoLin;
                $lin->token = $presusdb[$i]->tokenLin;
                $lin->varios1 = $presusdb[$i]->varios1Lin;
                $lin->varios2 = $presusdb[$i]->varios2Lin;
                $lin->varios3 = $presusdb[$i]->varios3Lin;
                $lin->varios4 = $presusdb[$i]->varios4Lin;
                $lin->varios5 = $presusdb[$i]->varios5Lin;
                $lin->varios6 = $presusdb[$i]->varios6Lin;
                $lin->varios7 = $presusdb[$i]->varios7Lin;
                $lin->varios8 = $presusdb[$i]->varios8Lin;
                $lin->varios9 = $presusdb[$i]->varios9Lin;
                $lin->varios10 = $presusdb[$i]->varios10Lin;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($presu) {
                $presu->presupuestoLines = $lins;
                $presus[] = $presu;
            }

            $rowcount = sizeof($presus);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($presus, $pagination->pageSize);

                $presus = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "presupuestos" => $presus?$presus:array());

    }

	function saveBudget($presupuesto) {
		//$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
		$this->load->model("usuario_model");
		$this->load->model("log_model");
		if (!isset($presupuesto->token)) {
			$presupuesto->token = getToken();
		}

		$result = $presupuesto->_save(false, false);
		
		if ($result) {
			if (isset($presupuesto->presupuestoLines)) {
				$presupuestoLines = $presupuesto->presupuestoLines;
				foreach ($presupuestoLines as $line) {
					$line->fk_presu_cab = $presupuesto->pk_presu_cab;
                    $line->fk_entidad = $presupuesto->fk_entidad;
		            //Nos aseguramos que los Tokens no existen
					if ($line->id_presu_lin == null) {
						$query = new stdClass();
						$this->db->where('token', $line->token);
                        $this->db->where('fk_entidad', $presupuesto->fk_entidad);
						$query = $this->db->get("presupuestos_lin");
						$budgetLine = $query->row();						
						if ($budgetLine) $line->id_presu_lin = $budgetLine->id_presu_lin;
					}
					if (!isset($line->token)) {
						$line->token = getToken();
					}
					$res = $line->_save(false, true);					
					if (!$res) throw new APIexception("Error on presupuesto_model->updateBudget. Unable to update Budget Line", ERROR_SAVING_DATA, serialize($presupuesto));
				}
			}
			//$this->db->trans_complete();			
			return true;
		} else {
			throw new APIexception("Error on presupuesto_model->updateBudget. Unable to update Budget", ERROR_SAVING_DATA, serialize($presupuesto));
		}	

	}

    /**
     * @param $entityPk
     *
     * Coge el siguiente numero de presupuesto de la serie predeterminada y actualiza la tabla
     */
    function getSerieForNewCode($entityPk) {

        $this->db->trans_start();

        $this->db->where('fk_entidad', $entityPk);
        $this->db->where('bool_predeterminada', 1);
        $this->db->where('anio = YEAR(NOW())');
        $query = $this->db->get('series');

        $result = $query->row(0, 'serie');

        if ($result) {
            $q = new stdClass();
            $q->num_presu = $result->num_presu + 1;
            $this->db->where('fk_entidad', $entityPk);
            $this->db->where('bool_predeterminada', 1);
            $this->db->where('anio = YEAR(NOW())');
            $this->db->where('serie', $result->serie);


            $this->db->update('series', $q);

            $this->db->trans_complete();

           $result->num_presu =  $result->num_presu + 1;
            return $result;

        } else {
            return null;
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
            $return = 'pk_presu_cab';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('presupuestos_cab');
        $this->db->where('presupuestos_cab.fk_entidad', $entityId);

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