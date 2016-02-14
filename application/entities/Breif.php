<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Brief extends eEntity {

    public $pk_brief;
    public $fk_pais;
    public $fk_cliente;
    public $cod_user;
    //public fk_marca;
    public $objetivo;
    public $fecha_inicio;
    public $fecha_fin;
    public $productos;
    public $fecha_solicitud;
    public $fecha_entrega;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_brief";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "briefs";
	}

}

?>