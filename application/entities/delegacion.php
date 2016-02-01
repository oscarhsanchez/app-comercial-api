<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class delegacion extends eEntity {

	public $pk_delegacion;
	public $fk_entidad;
	public $cod_delegacion;
	public $fk_provincia_entidad;
	public $descripcion;
	public $direccion;
	public $poblacion;
	public $codpostal;
	public $mail;
	public $telefono_fijo;
	public $telefono_movil;
	public $dropsize;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;		

	public function getPK() {
		return array ("pk_delegacion");
	}
	public function setPK() {
		if (isset($this->cod_delegacion) && isset($this->fk_entidad)) $this->pk_delegacion = $this->cod_delegacion . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "delegaciones";
	}

}

?>