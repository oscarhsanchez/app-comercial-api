<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class LogOrdenTrabajo extends eEntity {

    public $id;
    public $fk_pais;
    public $codigo_user;
    public $fk_orden_trabajo;
    public $fecha;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "id";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "log_ordenes_trabajo";
	}

}

?>