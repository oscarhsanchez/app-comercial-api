<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articuloFavorito extends eEntity {

	public $pk_favorito;
	public $fk_entidad;
	public $fk_cliente;
	public $fk_articulo;
	public $estado;
	public $token;
	
	public function getPK() {
		return array ("pk_favorito");
	}
	public function setPK() {
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "cliente_art_favorito";
	}

}

?>