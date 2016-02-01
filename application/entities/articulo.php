<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articulo extends eEntity {

	public $pk_articulo;
	public $fk_entidad;
	public $fk_subfamilia;
	public $cod_articulo;
	public $descripcion;
	public $descripcion_larga;
	public $precio_venta;
    public $precio_punto_verde;
	public $iva;
	public $re;
	public $control_stock;
    public $unidad_basica;
    public $unidad_venta;
    public $factor_agrupacion;
    public $unidades_pale;
    public $bool_modif_tarifa_desc;
    public $bool_control_lote;
    public $precio_venta_minimo;
    public $descuento_maximo;
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
    public $propiedades;
    public $bool_disponible_web;
    public $bool_restringir_ventas_por_unidades;
    public $precio_web;
    public $iva_web;
    public $re_web;
    public $descuento_web;
    public $fecha_baja;
    public $fk_marca_articulo;
    public $fk_articulo_imagen;
    public $tpv_stock_disponibilidad;
    public $tpv_stock_alerta;
    public $tpv_stock_warning;
    public $tpv_stock_disponible;
    public $bool_restringir_unidades_enteras;
    public $codigo_ean;
	public $token;
	
	public function getPK() {
		return array ("pk_articulo");
	}
	public function setPK() {
		if (isset($this->cod_articulo) && isset($this->fk_entidad)) $this->pk_articulo = $this->cod_articulo . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "articulos";
	}

}

?>