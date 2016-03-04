<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Ubicacion extends eEntity {

    public $pk_ubicacion;
    public $fk_pais;
    public $fk_empresa;
    public $unidad_negocio;
    public $tipo_medio;
    public $fk_plaza;
    public $fk_zona_fijacion;
    public $fk_zona_instalacion;
    public $fk_zona_iluminacion;
    public $estatus;
    public $ubicacion;
    public $direccion_comercial;
    public $referencia;
    public $categoria;
    public $catorcena;
    public $anio;
    public $fecha_instalacion;
    public $observaciones;
    public $trafico_vehicular;
    public $trafico_transeuntes;
    public $nivel_socioeconomico;
    public $lugares_cercanos;
    public $latitud;
    public $longitud;
    public $reserva;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_ubicacion";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "ubicaciones";
	}

}

?>