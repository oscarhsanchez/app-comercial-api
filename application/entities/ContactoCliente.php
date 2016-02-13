<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class ContactoCliente extends eEntity {

    public $pk_contacto_cliente;
    public $fk_cliente;
    public $fk_pais;
    public $nombre;
    public $apellidos;
    public $titulo;
    public $cargo;
    public $telefono;
    public $celular;
    public $email;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_contacto_cliente";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "contactos_clientes";
	}

}

?>