<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class AccionCliente extends eEntity {

    public $pk_accion;
    public $fk_pais;
    public $fk_cliente;
    public $fk_tipo_accion;
    public $cod_user;
    public $fecha;
    public $hora;
    public $titulo;
    public $resumen;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_accion";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "acciones_clientes";
	}

}

?>