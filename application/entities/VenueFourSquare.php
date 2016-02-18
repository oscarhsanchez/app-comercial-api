<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class VenueFourSquare extends eEntity {

    public $id;
    public $fk_pais;
    public $fk_category;
    public $fk_ubicacion;
    public $name;
    public $phone;
    public $lat;
    public $lon;
    public $distance;
    public $checkinscount;
    public $userscount;
    public $tipcount;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "id";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "meta_ubicacion_fq";
	}

}

?>