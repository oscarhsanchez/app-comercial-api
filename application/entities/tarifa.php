<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class tarifa extends eEntity {

    public $pk_tarifa;
    public $fk_entidad;
    public $cod_tarifa;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("pk_tarifa");
    }
    public function setPK() {
        if (isset($this->cod_tarifa) && isset($this->fk_entidad)) $this->pk_tarifa = $this->cod_tarifa . "_" . $this->fk_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "tarifas";
    }

}