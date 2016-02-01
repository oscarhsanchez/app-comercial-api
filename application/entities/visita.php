<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class visita extends eEntity {

	public $id;
	public $fk_entidad;
	public $fk_vendedor;
	public $fk_canal_venta;
	public $fk_cliente;
	public $fk_vendedor_reasignado;
	public $cod_vendedor;
	public $cod_canal_venta;
	public $cod_cliente;
	public $tipo_visita;
	public $fecha_visita;
	public $nombre_cliente;
	public $hora_visita;
	public $hora_ejecucion;
	public $fecha_calculada;
	public $bool_resultado;
	public $bool_visitada;
	public $cod_vendedor_reasignado;
	public $longitud;
	public $latitud;
	public $estado;
    public $fk_mot_no_venta;
	public $created_at;
	public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("id");
	}
	
	public function setPK() {
	}
	
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "visita";
	}

}

?>