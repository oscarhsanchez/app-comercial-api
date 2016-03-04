<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class RestriccionUbicacion extends eEntity {

    public $pk_restriccion_ubicacion;
    public $fk_pais;
    public $fk_ubicacion;
    public $fk_cliente;
    public $fk_categoria_propuesta;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_restriccion_ubicacion";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "restricciones_ubicaciones";
	}

}

?>