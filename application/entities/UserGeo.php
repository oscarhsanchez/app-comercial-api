<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class UserGeo extends eEntity {

    public $pk_user_geo;
    public $fk_user;
    public $fecha;
    public $longitud;
    public $latitud;


	public function getPK() {
		return "pk_user_geo";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ();
	}

	public function getTableName() {
		return "user_geo";
	}

}

?>