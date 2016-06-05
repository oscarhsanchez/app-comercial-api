<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class AgrupacionMedioDetalle extends eEntity {

    public $pk_agrupacion_detalle;
    public $fk_pais;
    public $fk_agruapcion;
    public $fk_medio;
    public $factor_agrupacion;
    public $created_at;
    public $updated_at;
    public $token;
    public $estado;

	public function getPK() {
		return "pk_agrupacion_detalle";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "agrupacion_medios_detalle";
	}

}

?>