<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class incidenciaPicking extends eEntity {
	
	public $id;
	public $fk_albaran;
    public $fk_entidad;
	public $fk_usuario_entidad;
    public $fk_linea_albaran;
    public $incidencia;
	public $cantidad;
	public $fecha;
    public $hora;
    public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	

	public function getPK() {
		return array ("id");
	}
	public function setPK() {
		//Autonumerico
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "incidencias_picking";
	}

}