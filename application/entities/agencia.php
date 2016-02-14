<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Agencia extends eEntity {

    public $pk_agencia;
    public $fk_pais;
    public $fk_empresa;
    public $razon_social;
    public $nombre_comercial;
    public $porcentaje_comision;
    public $dias_credito;
    public $credito_maximo;
    public $estatus;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_agencia";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "agencias";
	}

}

?>