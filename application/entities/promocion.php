<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class promocion extends eEntity {

    public $id;
    public $fk_entidad;
    public $titulo;
    public $descripcion;
    public $codigo_campana;
    public $fecha_inicio;
    public $fecha_fin;
    public $obligatoria;
    public $acumulable;
    public $multiplicable;
    public $imagen;
    public $tipo_web;
    public $fk_proveedor;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("id");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "promocion";
    }

}