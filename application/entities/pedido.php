<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class pedido extends eEntity {
	
	public $pk_pedido;
	public $fk_entidad;
	public $fk_usuario;
	public $serie;
	public $anio;
	public $fk_serie_entidad;
	public $fk_albaran_destino;
	public $fk_cliente;
	public $fk_delegacion;
	public $fk_terminal_tpv;
	public $fk_forma_pago;
	public $fk_condicion_pago;
    public $fk_repartidor;
    public $fk_almacen;
    public $fk_proveedor;
    public $token_visita;
    public $tipo_pedido;
    public $fecha_entrega;
    public $hora_entrega;
    public $hora;
	public $cod_pedido;	
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
    public $imp_promocion_cab;
	public $imp_iva_tot;
	public $imp_re_tot;
	public $imp_total;
	public $observaciones;
    public $codigo_ean;
    public $bool_confirmado;
    public $bool_conf_pago_tarjeta;
    public $importe_tarjeta;
    public $bool_pdp;
    public $puntos;
    public $imp_puntos;
    public $valor_punto;
    public $tarjeta;
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
	public $created_at;
	public $updated_at;
	public $token;

	public function getPK() {
		return array ("pk_pedido");
	}
	public function setPK() {
		if (isset($this->cod_pedido) && isset($this->fk_entidad)) $this->pk_pedido = $this->cod_pedido . "_" . $this->id_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "pedidos_cab";
	}

}