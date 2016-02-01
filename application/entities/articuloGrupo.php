<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articuloGrupo extends eEntity {

	public $pk_art_grupos;
	public $fk_entidad;
	public $cod_grupo;
	public $descripcion;
    public $icono;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("pk_art_grupos");
	}
	public function setPK() {
		if (isset($this->cod_grupo) && isset($this->fk_entidad)) $this->pk_art_grupos = $this->cod_grupo . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "art_grupos";
	}

}

?>