<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class zona extends eEntity {

    public $pk_cliente_zona;
    public $fk_entidad;
    public $fk_delegacion;
    public $cod_zona;
    public $name;
    public $description;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("pk_cliente_zona");
    }
    public function setPK() {
        if (isset($this->cod_zona) && isset($this->fk_entidad)) $this->pk_cliente_zona = $this->cod_zona . "_" . $this->id_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "cliente_zonas";
    }

}