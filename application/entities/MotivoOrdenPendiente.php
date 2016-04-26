<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class MotivoOrdenPendiente extends eEntity {

    public $pk_motivo;
    public $fk_pais;
    public $descripcion;
    public $tipo_incidencia;
    public $created_at;
    public $updated_at;
    public $token;
    public $estado;


    public function getPK() {
        return "pk_motivo";
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "updated_at");
    }

    public function getTableName() {
        return "motivos_ordenes_pendientes";
    }

} 