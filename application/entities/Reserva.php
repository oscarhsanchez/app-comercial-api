<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Reserva extends eEntity {

    public $pk_reserva;
    public $fk_pais;
    public $fk_empresa;
    public $fk_ubicacion;
    public $fk_propuesta;
    public $posicion;
    public $catorcena;
    public $fecha_inicio;
    public $fecha_fin;
    public $fecha_reserva;
    public $estatus;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_reserva";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
        return "reservas";
	}

}

?>