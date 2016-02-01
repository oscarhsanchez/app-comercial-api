<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class subzona extends eEntity {

    public $pk_cliente_subzona;
    public $fk_cliente_zona;
    public $fk_entidad;
    public $cod_subzona;
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
        if (isset($this->cod_subzona) && isset($this->fk_entidad)) $this->pk_cliente_subzona = $this->cod_subzona . "_" . $this->id_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "cliente_subzonas";
    }

}