<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class albaran extends eEntity {
	
	public $pk_albaran;
	public $fk_entidad;
	public $fk_cliente;
	public $fk_usuario_entidad;
    public $tipo_pedido;
	public $serie;
	public $anio;
	public $fk_serie_entidad;
	public $fk_factura_destino;
	public $fk_almacen;
	public $fk_delegacion;
	public $fk_terminal_tpv;
	public $fk_forma_pago;
	public $fk_condicion_pago;
    public $fk_repartidor;
    public $fk_repartidor_reasignado;
    public $fecha_entrega;
    public $hora_entrega;
    public $picking_fecha;
    public $picking_hora;
    public $picking_estado;
	public $cod_albaran;
	public $cod_cliente;
	public $cod_usuario_entidad;
	public $cod_almacen;
	public $cod_delegacion;
	public $cod_terminal_tpv;
	public $cod_forma_pago;
	public $cod_condicion_pago;
	public $num_serie;
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
    public $imp_promocion_cab;
	public $imp_iva_tot;
	public $imp_re_tot;
	public $imp_total;
	public $observaciones;
    public $fk_proveedor;
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
    public $fecha_pedido;
    public $hora_pedido;
    public $token_visita;
    public $token_archivo;
    public $bool_entregado;
	public $created_at;
	public $updated_at;
    public $token;


	public function getPK() {
		return array ("pk_albaran");
	}
	public function setPK() {
		if (isset($this->cod_albaran) && isset($this->fk_entidad)) $this->pk_albaran = $this->cod_albaran . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "albaranes_cab";
	}

}