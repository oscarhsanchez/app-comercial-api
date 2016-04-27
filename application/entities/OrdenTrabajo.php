<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class OrdenTrabajo extends eEntity {

    public $pk_orden_trabajo;
    public $fk_pais;
    public $fk_propuesta;
    public $fk_medio;
    public $fk_motivo;
    public $codigo_user;
    public $tipo;
    public $estado_orden;
    public $fecha_limite;
    public $fecha_cierre;
    public $observaciones;
    public $observaciones_cierre;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_orden_trabajo";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "ordenes_trabajo";
	}

}

?>