<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class User extends eEntity {

    public $id;
    public $username;
    public $username_canonical;
    public $codigo;
    public $email;
    public $email_canonical;
    public $enabled;
    public $salt;
    public $password;
    public $last_login;
    public $locked;
    public $expired;
    public $expires_at;
    public $confirmation_token;
    public $password_requested_at;
    public $roles;
    public $credentials_expired;
    public $credentials_expire_at;
    public $name;
    public $surnames;
    public $avatar;
    public $phone;
    public $token;
    public $active;
    public $created_at;
    public $updated_at;

	public function getPK() {
		return "id";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "user";
	}

}

?>