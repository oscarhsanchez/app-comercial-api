<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Incidencia extends eEntity {

    public $pk_incidencia;
    public $fk_pais;
    public $fk_medio;
    public $codigo_user;
    public $codigo_user_asignado;
    public $tipo;
    public $estado_incidencia;
    public $fecha_limite;
    public $fecha_cierre;
    public $observaciones;
    public $observaciones_cierre;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_incidencia";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "incidencias";
	}

}

?>