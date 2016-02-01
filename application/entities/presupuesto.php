<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class presupuesto extends eEntity {
	
	public $pk_presu_cab;
	public $fk_entidad;
	public $fk_usuario;
	public $serie;
	public $anio;
    public $fk_pedido_destino;
	public $fk_serie_entidad;
	public $fk_cliente;
	public $fk_delegacion;
	public $fk_terminal_tpv;
	public $fk_forma_pago;
	public $fk_condicion_pago;
	public $cod_presupuesto;
	public $cod_usuario_entidad;
	public $num_serie;	
	public $cod_cliente;
	public $cod_delegacion;
	public $cod_terminal_tpv;
	public $cod_forma_pago;
	public $cod_condicion_pago;	
	public $bool_actualiza_numeracion;
	public $bool_recalcular;
	public $fecha;
	public $raz_social;
	public $nif;
	public $direccion;
	public $poblacion;
	public $provincia;
	public $codpostal;
	public $base_imponible_tot;
	public $imp_desc_tot;
    public $imp_promo_lin_total;
	public $imp_iva_tot;
	public $imp_re_tot;
	public $imp_total;
	public $observaciones;
	public $varios1;
	public $varios2;
	public $varios3;
	public $varios4;
	public $varios5;
	public $varios6;
	public $varios7;
	public $varios8;
	public $varios9;
	public $varios10;
	public $estado;
    public $bool_presu_activo;
	public $created_at;
	public $updated_at;
	public $token;
	

	public $fk_pedido;
	public $cod_pedido;
	public $token_pedido;
	
	public $fk_albaran;
	public $cod_albaran;
	public $token_albaran;

	public $fk_factura;
	public $cod_factura;
	public $token_factura;		

	public function getPK() {
		return array ("pk_presu_cab");
	}

	public function setPK() {
		if (isset($this->cod_presupuesto) && isset($this->fk_entidad)) $this->pk_presu_cab = $this->cod_presupuesto . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at", "fk_pedido", "cod_pedido", "token_pedido", "fk_albaran", "cod_albaran", "token_albaran", "fk_factura", "cod_factura", "token_factura");
	}

	public function getTableName() {
		return "presupuestos_cab";
	}

    public function calculate($lines) {
        $this->base_imponible_tot = 0.0;
        $this->imp_desc_tot = 0.0;
        $this->imp_promo_lin_total = 0.0;
        $this->imp_iva_tot = 0.0;
        $this->imp_re_tot = 0.0;
        $this->imp_total = 0.0;
        $ivas = array();
        $res = array();

        if ($lines) {
            foreach ($lines as $line) {
                if ($line->estado) {
                    $this->base_imponible_tot += $line->base_imponible;
                    $this->imp_desc_tot += $line->imp_descuento;
                    $this->imp_promo_lin_total += $line->imp_promocion;

                    if ($line->iva && $line->iva > 0 && !array_key_exists(strval($line->iva), $ivas))
                        $ivas[strval($line->iva)] = $line->base_imponible*$line->iva/100;
                    else if ($line->iva && $line->iva > 0)
                        $ivas[strval($line->iva)] += $line->base_imponible*$line->iva/100;

                    if ($line->re && $line->re > 0 && !array_key_exists(strval($line->re), $res))
                        $res[strval($line->re)] = $line->base_imponible*$line->re/100;
                    else if ($line->re && $line->re > 0)
                        $res[strval($line->re)] += $line->base_imponible*$line->re/100;
                }

            }
        }

        foreach ($ivas as $iva) {
            $this->imp_iva_tot += round($iva, 2);
        }
        foreach ($res as $re) {
            $this->imp_re_tot += round($re, 2);
        }

        $this->imp_total = round($this->base_imponible_tot, 2) + round($this->imp_iva_tot, 2) + round($this->imp_re_tot, 2);

    }

}