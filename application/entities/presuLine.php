<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class presuLine extends eEntity {
	
	public $id_presu_lin;
    public $fk_entidad;
    public $fk_articulo;
	public $fk_presu_cab;
    public $fk_promocion;
	public $fk_usuario;
	public $cod_usuario_entidad;	
	public $cod_concepto;
	public $concepto;
	public $cantidad;
	public $precio;
	public $base_imponible;
	public $descuento;
	public $imp_descuento;
    public $desc_promocion;
    public $imp_promocion;
	public $iva;
	public $imp_iva;
	public $re;
	public $imp_re;
	public $total_lin;
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
		return array ("id_presu_lin");
	}

	public function setPK() {
		//Autonumerico
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "presupuestos_lin";
	}

}