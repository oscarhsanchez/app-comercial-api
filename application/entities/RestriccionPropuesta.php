<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class RestriccionPropuesta extends eEntity {

    public $pk_restriccion_propuesta;
    public $fk_pais;
    public $fk_propuesta;
    public $fk_cliente;
    public $fk_categoria_propuesta;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_restriccion_propuesta";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "restricciones_propuestas";
	}

}

?>