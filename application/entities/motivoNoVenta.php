<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class motivoNoVenta extends eEntity {

    public $pk_mot_no_venta;
    public $fk_entidad;
    public $cod_motivo_no_venta;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("pk_mot_no_venta");
    }
    public function setPK() {
        if (isset($this->cod_motivo_no_venta) && isset($this->fk_entidad)) $this->pk_mot_no_venta = $this->cod_motivo_no_venta . "_" . $this->fk_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "motivos_no_venta";
    }

}