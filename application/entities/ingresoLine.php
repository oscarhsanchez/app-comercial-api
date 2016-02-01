<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class ingresoLine extends eEntity {
	
	public $id_ingreso_lin;
	public $fk_otroingr_cab;
	public $fk_usuario;
    public $fk_entidad;
    public $cod_usuario_entidad;
	public $cod_concepto;
	public $concepto;
	public $cantidad;
	public $precio;
	public $base_imponible;
	public $descuento;
	public $imp_descuento;
	public $iva;
	public $imp_iva;
	public $re;
	public $imp_re;
	public $retencion;
	public $imp_retencion;
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
		return array ("id_ingreso_lin");
	}
	public function setPK() {
		//Autonumerico
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "ingresos_lin";
	}

}