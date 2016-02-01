<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class motivoPromocion extends eEntity {

    public $pk_motivo_promocion;
    public $fk_entidad;
    public $cod_motivo_promocion;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("pk_motivo_promocion");
    }
    public function setPK() {
        if (isset($this->cod_motivo_promocion) && isset($this->fk_entidad)) $this->pk_motivo_promocion = $this->cod_motivo_promocion . "_" . $this->fk_entidad;
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "motivos_promocion";
    }

}