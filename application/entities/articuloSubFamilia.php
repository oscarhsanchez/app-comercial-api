<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articuloSubFamilia extends eEntity {

	public $pk_art_subfamilias;
	public $fk_entidad;
	public $fk_familia;
	public $cod_subfamilia;
    public $icono;
	public $descripcion;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("pk_art_subfamilias");
	}
	public function setPK() {
		if (isset($this->cod_subfamilia) && isset($this->fk_entidad)) $this->pk_art_subfamilias = $this->cod_subfamilia . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "art_subfamilias";
	}

}

?>