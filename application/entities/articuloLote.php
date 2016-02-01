<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articuloLote extends eEntity {

	public $pk_lote;
	public $fk_entidad;
	public $fk_articulo;
    public $fk_almacen;
    public $lote;
    public $fecha_fabricacion;
    public $fecha_caducidad;
    public $codigo_ean;
    public $stock;
	public $token;
    public $estado;
	
	public function getPK() {
		return array ("pk_lote");
	}
	public function setPK() {

	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "articulos_lotes";
	}

}

?>