<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class proveedorValoracion extends eEntity {

    public $pk_valoracion;
    public $fk_entidad;
    public $fk_cliente;
    public $fk_proveedor;
    public $fecha;
    public $valoracion;
    public $estado;
    public $token;
    public $comentarios;


    public function getPK() {
        return array ("pk_valoracion");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "proveedor_valoracion";
    }

}