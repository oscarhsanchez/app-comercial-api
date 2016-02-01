<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class factura extends eEntity {
	
	public $pk_factura;
	public $fk_entidad;
	public $serie;
	public $anio;
    public $bool_origen_albaran;
	public $fk_serie_entidad;
	public $fk_cliente;
    public $fk_cliente_facturacion;
	public $fk_delegacion;
	public $fk_terminal_tpv;
	public $fk_fact_anul;
	public $fk_forma_pago;
	public $fk_condicion_pago;
	public $fk_almacen;
    public $fk_usuario_entidad;
	public $cod_factura;
	public $cod_usuario_entidad;
	public $num_serie;
	public $cod_cliente;
	public $cod_delegacion;
	public $cod_terminal_tpv;
	public $cod_forma_pago;
	public $cod_condicion_pago;
	public $cod_almacen;	
	public $bool_actualiza_numeracion;
	public $bool_recalcular;
	public $fecha;
	public $fecha_vencimiento;
    public $fk_repartidor;
    public $fk_repartidor_reasignado;
	public $raz_social;
	public $nif;
	public $direccion;
	public $poblacion;
	public $provincia;
	public $codpostal;
	public $base_imponible_tot;
    public $desc_promocion_cab;
    public $imp_promocion_cab;
    public $imp_promo_lin_total;
	public $imp_desc_tot;
	public $imp_iva_tot;
	public $imp_re_tot;
	public $imp_retencion_tot;
	public $imp_total;
	public $repetir_cada;
	public $period_tipo_mensual;
	public $period_values_mes;
	public $period_dia_1;
	public $period_dia_2;
	public $period_dia_3;
	public $period_dia_4;
	public $period_dia_5;
	public $period_dia_6;
	public $period_dia_7;
	public $envios_mails;
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
	public $estado_factura;
    public $bool_conf_pago_tarjeta;
    public $importe_tarjeta;
    public $bool_pdp;
    public $puntos;
    public $imp_puntos;
    public $valor_punto;
    public $estado;
	public $created_at;
	public $updated_at;
	public $token;

	public function getPK() {
		return array ("pk_factura");
	}
	public function setPK() {
		if (isset($this->cod_factura) && isset($this->fk_entidad)) $this->pk_factura = $this->cod_factura . "_" . $this->id_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "facturas_cab";
	}

}