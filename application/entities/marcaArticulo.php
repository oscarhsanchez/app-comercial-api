<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class marcaArticulo extends eEntity {

	public $pk_marca_articulo;
	public $fk_entidad;
	public $codigo;
	public $descripcion;
    public $imagen;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("pk_marca_articulo");
	}
	public function setPK() {
		if (isset($this->codigo) && isset($this->fk_entidad)) $this->pk_marca_articulo = $this->codigo . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "marca_articulo";
	}

}

?>