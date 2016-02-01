<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class r_usu_cli extends eEntity {

	public $pk_usuario_cliente;
	public $fk_entidad;
	public $fk_cliente;
	public $fk_canal_venta;
	public $fk_usuario_vendedor;
	public $fk_usuario_repartidor;
	public $fk_usuario_receptor_vendedor;
	public $fk_usuario_receptor_repartidor;
	public $fk_delegacion;
	public $fecha_repartidor_desde;
	public $fecha_repartidor_hasta;
	public $fecha_vendedor_desde;
	public $fecha_vendedor_hasta;
	public $tipo_frecuencia;
	public $repetir_cada;
	public $fecha_inicio;
	public $tipo_mensual;
	public $values_mes;
	public $hora;
	public $dia_1;
	public $dia_2;
	public $dia_3;
	public $dia_4;
	public $dia_5;
	public $dia_6;
	public $dia_7;
	public $hora_reparto;
	public $estado;
	public $created_at;
	public $updated_at;
	public $updated_vendedor_at;
	public $updated_repartidor_at;
	public $token;
	
	public function getPK() {
		return array ("pk_usuario_cliente");
	}
	
	public function setPK() {
	}
	
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "r_usu_cli";
	}

}

?>