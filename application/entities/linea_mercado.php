<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class linea_mercado extends eEntity {

    public $pk_linea_mercado;
    public $fk_entidad;
    public $cod_linea_mercado;
    public $name;
    public $description;
    public $dropsize;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("pk_linea_mercado");
    }
    public function setPK() {
        if (isset($this->cod_linea_mercado) && isset($this->fk_entidad)) $this->pk_linea_mercado = $this->cod_linea_mercado . "_" . $this->id_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "lineas_mercado";
    }

}