<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class ComisionAgencia extends eEntity {

    public $pk_comision_agencia;
    public $fk_agencia;
    public $fk_pais;
    public $fk_empresa;
    public $unidad_negocio;
    public $porcentaje_comision;
    public $created_at;
    public $updated_at;
    public $token;
    public $estado;


	public function getPK() {
		return "pk_comision_agencia";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "agencias_comisiones";
	}

}

?>