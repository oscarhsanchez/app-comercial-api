<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class TipoAccion extends eEntity {

    public $pk_tipo_accion;
    public $fk_pais;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_tipo_accion";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "tipos_acciones";
	}

}

?>