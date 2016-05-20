<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class PropuestaDetalleOutdoor extends eEntity {

    public $pk_propuesta_detalle_outdoor;
    public $fk_propuesta_detalle;
    public $fk_pais;
    public $fk_medio;
    public $fk_categoria_propuesta;
    public $fecha_inicio;
    public $fecha_fin;
    public $catorcena;
    public $tipo_negociacion;
    public $posicion_medio;
    public $moneda;
    public $precio;
    public $tipo_cambio;
    public $pautado;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_propuesta_detalle_outdoor";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "propuestas_detalle_outdoor";
	}

}

?>