<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class pedidoProveedorLin extends eEntity {
	
	public $id_pedido_proveedor_lin;
	public $fk_pedido_proveedor_cab;
	public $fk_articulo;
	public $cod_articulo;
	public $unidades_solicitadas;
	public $unidades_entregadas;
    public $precio;
    public $iva;
    public $unidades_pendientes_venta;
    public $precio_coste;
    public $cod_art_prov;
	public $created_at;
	public $updated_at;
	public $estado;
	public $token;

	public function getPK() {
		return array ("id_pedido_proveedor_lin");
	}
	public function setPK() {
		
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "pedido_proveedor_lin";
	}

}