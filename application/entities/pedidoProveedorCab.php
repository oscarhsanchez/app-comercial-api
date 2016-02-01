<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class pedidoProveedorCab extends eEntity {
	
	public $pk_pedido_proveedor_cab;
	public $fk_proveedor;
    public $fk_almacen;
	public $fk_entidad;
	public $fk_usuario;
	public $cod_pedido_proveedor;
	public $comentario;
	public $fecha;
	public $hora;
    public $fecha_entrega;
    public $coste_envio;
    public $imp_total;
	public $created_at;
	public $updated_at;
	public $estado;
	public $token;


	public function getPK() {
		return array ("pk_pedido_proveedor_cab");
	}
	public function setPK() {
        if (isset($this->cod_pedido_proveedor) && isset($this->fk_entidad)) $this->pk_pedido_proveedor_cab = $this->cod_pedido_proveedor . "_" . $this->fk_entidad;
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "pedido_proveedor_cab";
	}

}