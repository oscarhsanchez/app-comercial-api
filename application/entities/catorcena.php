<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Catorcena extends eEntity {

    public $id;
    public $fecha_inicio;
    public $fecha_fin;
    public $anio;
    public $catorcena;
    public $mes;
    public $mes_numero;
    public $catorcena_inicio;
    public $catorcena_termino;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "id";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "catorcenas";
	}

}

?>