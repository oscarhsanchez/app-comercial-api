<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class ComisionEjecutivo extends eEntity {

    public $pk_comision_ejecutivo;
    public $cod_user;
    public $fk_pais;
    public $fk_empresa;
    public $unidad_negocio;
    public $fk_cliente;
    public $porcentaje_comision;
    public $created_at;
    public $updated_at;
    public $token;
    public $estado;


	public function getPK() {
		return "pk_comision_ejecutivo";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "ejecutivos_comisiones";
	}

}

?>