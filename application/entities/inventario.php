<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class inventario extends eEntity {

    public $pk_inventario_cab;
    public $fk_entidad;
    public $fk_usuario;
    public $fk_almacen;
    public $cod_inventario;
    public $comentario;
    public $fecha;
    public $hora;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    public $num_codigo; //No se guarda en bbdd. Solo la utilizamos para actualizar la numeracion en r_usu_emp

    public function getPK() {
        return array ("pk_inventario_cab");
    }
    public function setPK() {
        if (isset($this->cod_inventario) && isset($this->fk_entidad)) $this->pk_inventario_cab = $this->cod_inventario . "_" . $this->fk_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at", "num_codigo");
    }

    public function getTableName() {
        return "inventario_cab";
    }

}