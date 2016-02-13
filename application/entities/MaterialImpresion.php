<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class MaterialImpresion extends eEntity {

    public $pk_material;
    public $material_nombre;
    public $fk_tipo_medio;
    public $fk_subtipo_medio;
    public $precio;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_material";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "materiales_impresion";
	}

}

?>