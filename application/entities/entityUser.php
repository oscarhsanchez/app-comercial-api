<?php

class entityUser {
	
	public $id_usuario;
	public $pass;
	public $salt;
	public $nombre;
	public $apellidos;
	public $tipo;
	public $mail;
	public $telefono;
	public $estado;
	public $last_login;
	public $token;	
	public $pk_usuario_entidad;
	public $fk_entidad;
	public $fk_tipo_agente;
    public $cod_tipo_agente;
	public $id_seg_rol;
	public $fk_delegacion;
	public $fk_almacen;
	public $fk_terminal_tpv;
	public $fk_canal_venta;
    public $cod_canal_venta;
	public $serie_id;
	public $serie_anio;
	public $fk_serie_entidad;
	public $cod_usuario_entidad;
    public $num_captacion;
    public $num_inventario;
    public $num_mov_almacen;
    public $num_incidencia;
	public $send_ddbb;
	public $upd_series;	
	public $estado_entity_user;
	public $token_entity_user;
    public $bool_search_server_client;
		
}