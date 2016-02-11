<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Session extends eEntity {

    public $id;
    public $fk_user;
    public $fk_pais;
    public $roles;
    public $access_token;
    public $renew_token;
    public $phone_id;
    public $created_at;
    public $updated_at;

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
        return "session";
    }

    public function getRoles() {
        return unserialize($this->roles);
    }

} 