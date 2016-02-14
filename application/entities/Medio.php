<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Medio extends eEntity {

    public $pk_medio;
    public $fk_pais;
    public $fk_ubicacion;
    public $fk_subtipo;
    public $posicion;
    public $id_cara;
    public $tipo_medio;
    public $estatus_iluminacion;
    public $estatus_inventario;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_medio";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "medios";
	}

}

?>