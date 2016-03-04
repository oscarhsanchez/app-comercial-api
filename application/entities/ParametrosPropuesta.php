<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class ParametrosPropuesta extends eEntity {

    public  $pk_parametros_propuesta;
    public $prespuesto;
    public $plazas;
    public $fecha_inicio;
    public $catorcenas;
    public $lugares_cercanos;
    public $lugares_cercanos_restriccion;
    public $tipologia;
    public $iluminacion;
    public $tipologia_medios;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_parametros_propuesta";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "parametros_propuestas";
	}

}

?>