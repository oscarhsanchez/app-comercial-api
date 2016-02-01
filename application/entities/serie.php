<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class serie extends eEntity {
	
	public $serie;
	public $anio;
	public $fk_entidad;
	public $bool_predeterminada;
	public $num_presu;
	public $num_pedido;
	public $num_albaran;
	public $num_factura;
	public $num_factura_rectif;
	public $num_otros_ingr;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	

	public function getPK() {
		return array ("serie", "anio", "id_entidad");
	}
	public function setPK() {
		//PK Compuesta
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "series";
	}

}