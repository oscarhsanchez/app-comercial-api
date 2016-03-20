<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class ImagenUbicacion extends eEntity {

    public $pk_archivo;
    public $fk_pais;
    public $fk_ubicacion;
    public $fk_medio;
    public $nombre;
    public $path;
    public $url;
    public $observaciones;
    public $observaciones_cliente;
    public $estado_imagen;
    public $estado;
    public $token;
    public $created_at;
    public $updated_at;


	public function getPK() {
		return "pk_archivo";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "imagenes_ubicaciones";
	}

}

?>