<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class forma_pago extends eEntity {

    public $pk_forma_pago;
    public $fk_entidad;
    public $cod_forma_pago;
    public $descripcion;
    public $bool_fecha_vencimiento;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    public function getPK() {
        return array ("pk_forma_pago");
    }
    public function setPK() {
        if (isset($this->cod_forma_pago) && isset($this->fk_entidad)) $this->pk_forma_pago = $this->cod_forma_pago . "_" . $this->fk_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "formas_pago";
    }

}

?>