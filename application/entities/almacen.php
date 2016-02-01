<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class almacen extends eEntity {

    public $pk_almacen;
    public $fk_entidad;
    public $fk_provincia_entidad;
    public $cod_almacen;
    public $bool_principal;
    public $descripcion;
    public $direccion;
    public $poblacion;
    public $codpostal;
    public $telefono_1;
    public $telefono_2;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    public function getPK() {
        return array ("pk_almacen");
    }
    public function setPK() {
        if (isset($this->cod_almacen) && isset($this->fk_entidad)) $this->pk_almacen = $this->cod_almacen . "_" . $this->fk_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "almacen";
    }

}

?>