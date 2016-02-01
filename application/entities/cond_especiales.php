<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class cond_especiales extends eEntity {

	public $id_cond_especial;
	public $fk_entidad;
	public $fk_cliente;
	public $fk_grupo;
	public $fk_familia;
	public $fk_subfamilia;
	public $fk_articulo;
	public $fk_agrupacion;
	public $tipo;
	public $fecha_desde;
	public $fecha_hasta;
	public $cod_camp;
	public $precio;
	public $descuento;
	public $vale_descuento;
	public $created_at;
	public $updated_at;
	public $estado;
	public $cod_cliente;
	public $token;
	
	public function getPK() {
		return array ("id_cond_especial");
	}
	
	public function setPK() {
	}
	
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "cond_especiales";
	}

}

?>