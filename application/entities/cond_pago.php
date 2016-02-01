<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class cond_pago extends eEntity {

	public $pk_condicion_pago;
	public $fk_entidad;
	public $cod_condicion_pago;
	public $descripcion;
	public $num_dias;
    public $bool_generar_factura_tpv;
	public $created_at;
	public $updated_at;
	public $estado;
	public $token;
	
	public function getPK() {
		return array ("pk_condicion_pago");
	}
	
	public function setPK() {
        if (isset($this->cod_condicion_pago) && isset($this->fk_entidad)) $this->pk_condicion_pago = $this->cod_condicion_pago . "_" . $this->id_entidad;
	}
	
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "cond_pago";
	}

}

?>