<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class restriccionArticulo extends eEntity {

    public $pk_articulo_restriccion;
    public $fk_cliente;
    public $fk_articulo;
    public $fk_grupo;
    public $fk_familia;
    public $fk_subfamilia;
    public $fk_agrupacion;
    public $fk_marca_articulo;
    public $fk_distribuidor;
    public $bool_permitir;
    public $estado;

    public function getPK() {
		return array ("pk_articulo_restriccion");
	}
	public function setPK() {

	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "art_restriccion";
	}

}

?>