<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class dispositivo extends eEntity {

	public $pk_terminal_tpv;
	public $fk_entidad;
	public $cod_terminal_tpv;
	public $descripcion;
	public $version_app;
	public $sistema_operativo;
	public $fabricante;
	public $modelo;
	public $tipo;
	public $fecha_compra;
	public $id_dispositivo;
	public $token_stores;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;


	public function getPK() {
		return array ("pk_terminal_tpv");
	}
	public function setPK() {
		if (isset($this->cod_terminal_tpv) && isset($this->fk_entidad)) $this->pk_terminal_tpv = $this->cod_terminal_tpv . "_" . $this->id_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "terminales_tpv";
	}

}

?>