<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class tipo_mpv extends eEntity {

    public $id;
    public $fk_entidad;
    public $cod_tipo_mpv;
    public $nombre;
    public $modelo;
    public $fabricante;
    public $ano;
    public $vencimiento;
    public $matricula;
    public $delegacionStock_id; //Tabla delegacion_stock para poder mostrar la relacion cliente - tipo mpv en terminal.
    public $stock; //Tabla delegacion_stock para poder mostar el stock de cada tipo en la delegacion.
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
        return array ("created_at", "update_at", "delegacionStock_id", "stock");
    }

    public function getTableName() {
        return "tipo_mpv";
    }

}