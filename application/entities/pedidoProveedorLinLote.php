<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class pedidoProveedorLinLote extends eEntity {

    public $pk_lote;
    public $fk_pedido_proveedor_lin;
    public $lote;
    public $fecha_fabricacion;
    public $fecha_caducidad;
    public $stock;
    public $estado;
    public $token;
    public $codigo_ean;

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
		return "pedido_proveedor_lin_lote";
	}

}