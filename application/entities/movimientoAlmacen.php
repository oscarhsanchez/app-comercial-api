<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class movimientoAlmacen extends eEntity {

    public $pk_movimientos_almacen_cab;
    public $fk_entidad;
    public $fk_almacen_ori;
    public $fk_almacen_des;
    public $fk_usuario;
    public $cod_movimientos_alm;
    public $comentario;
    public $fecha;
    public $hora;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    public $num_codigo; //No se guarda en bbdd. Solo la utilizamos para actualizar la numeracion en r_usu_emp


    public function getPK() {
        return array ("pk_movimientos_almacen_cab");
    }
    public function setPK() {
        if (isset($this->cod_movimientos_alm) && isset($this->fk_entidad)) $this->pk_movimientos_almacen_cab = $this->cod_movimientos_alm . "_" . $this->fk_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at", "num_codigo");
    }

    public function getTableName() {
        return "movimientos_almacen_cab";
    }

}