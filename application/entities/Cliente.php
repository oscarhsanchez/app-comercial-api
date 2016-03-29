<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Cliente extends eEntity {

    public $pk_cliente;
    public $fk_pais;
    public $fk_empresa;
    public $codigo_user;
    public $rfc;
    public $razon_social;
    public $nombre_comercial;
    public $direccion;
    public $domicilio_calle;
    public $domicilio_no_exterior;
    public $domicilio_no_interior;
    public $domicilio_colonia;
    public $domicilio_delegacion;
    public $domicilio_estado;
    public $domicilio_pais;
    public $domicilio_cp;
    public $telefono;
    public $porcentaje_comision;
    public $dias_credito;
    public $credito_maximo;
    public $estatus;
    public $created_at;
    public $updated_at;
    public $token;
    public $estado;
		

	public function getPK() {
		return "pk_cliente";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "clientes";
	}

}

?>