<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articuloFamilia extends eEntity {

	public $pk_art_familias;
	public $fk_entidad;
	public $fk_grupo;
	public $cod_familia;
	public $descripcion;
    public $icono;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("pk_art_familias");
	}
	public function setPK() {
		if (isset($this->cod_familia) && isset($this->fk_entidad)) $this->pk_art_familias = $this->cod_familia . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "art_familias";
	}

}

?>