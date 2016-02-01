<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class entity extends eEntity {
	
	public $pk_entidad;
	public $codigo;
	public $id_propietario;
	public $id_pais;
	public $id_provincia;
	public $estado;
	public $nombre;
	public $apellidos;
	public $nombre_comercial;
	public $raz_social;
	public $nif;
	public $direccion;
	public $poblacion;
	public $codPostal;
	public $mail;
	public $telefono_fijo;
	public $telefono_movil;
	public $fax;
	public $web;
	public $datos_registro_mercantil;
	public $tipo;
	public $logo;
	public $imp_iva_ini_comp;
	public $fecha_ini_actividad;
	public $fecha_ini_fiscal;
	public $fecha_fin_fiscal;
	public $regimen_iva;
	public $ins_reg_dev_men;
	public $ins_reg_ope_int;
	public $anagrama_hacienda;
	public $estab_terri_nacio;
	public $dev_iva_mensual;
	public $oper_intracomunitarias;
	public $estimacion_irpf;
	public $obli_mod_130_act_eco;
	public $fec_obli_mod_130_act_eco;
	public $obli_mod_130_entidad;
	public $fec_obli_mod_130_entidad;
	public $obli_mod_111_trabajadores;
	public $fec_obli_mod_111_trabajadores;
	public $obli_mod_111_actividades;
	public $fec_obli_mod_111_actividades;
	public $obli_mod_115;
	public $fec_obli_mod_115;
	public $texto_por_defecto_mal;
	public $pie_documentos;
	public $region_fiscal;
	public $irpf_x_defecto;
	public $iva_x_defecto;
	public $re_x_defecto;
	public $saldo_inicial_caja;
	public $token;
	public $secret_key;
    public $jasper_informe_presupuesto_id;
    public $jasper_informe_presupuesto_ruta;
    public $jasper_informe_pedido_id;
    public $jasper_informe_pedido_ruta;
    public $jasper_informe_albaran_id;
    public $jasper_informe_albaran_ruta;
    public $jasper_informe_factura_id;
    public $jasper_informe_factura_ruta;
    public $jasper_informe_pedidoproveedor_id;
    public $jasper_informe_pedidoproveedor_ruta;

	public function getPK() {
		return array ("pk_entidad");
	}
	public function setPK() {
		//autonumerico
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "entidades";
	}

}