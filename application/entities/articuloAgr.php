<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articuloAgr extends eEntity {

	public $pk_art_agrupaciones;
	public $fk_entidad;
	public $cod_agrupacion_articulo;
	public $descripcion;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("pk_art_agrupaciones");
	}
	public function setPK() {
		if (isset($this->cod_agrupacion_articulo) && isset($this->fk_entidad)) $this->pk_art_agrupaciones = $this->cod_agrupacion_articulo . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "art_agrupaciones";
	}

}

?>