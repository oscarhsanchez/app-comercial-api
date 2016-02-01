<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_R_USU_CLI);


class cliente_canal_venta_model extends CI_Model {


    /**
     * Devuelve r_usu_cli a partir de un canal de venta.
     *
     * @param $canalVentaPk
     *
     * @return r_usu_cli
     */
    function getRUsuCliByCanalVentaAndCliente($clientPk, $canalVentaPk) {
        $this->db->where('fk_canal_venta', $canalVentaPk);
        $this->db->where('fk_cliente', $clientPk);
        $this->db->where('estado > 0');
        $query = $this->db->get('r_usu_cli');

        $r_usu_cli = $query->row(0, 'r_usu_cli');
        return $r_usu_cli;

    }

    /**
     * Establece una relacion entre un cliente y un canal de venta.
     *
     * @param $fk_entidad
     * @param $clientPk
     * @param $userPk
     * @param $canalVentaPk
     *
     * @return r_usu_cli
     */
    function set($fk_entidad, $clientPk, $canalVentaPk) {

        $r_usu_cli = $this->getRUsuCliByCanalVentaAndCliente($clientPk, $canalVentaPk);

        if (!$r_usu_cli) {

            $r_usu_cli = new r_usu_cli();
            $r_usu_cli->fk_canal_venta = $canalVentaPk;
            $r_usu_cli->fk_cliente = $clientPk;
            $r_usu_cli->fk_entidad = $fk_entidad;
            $r_usu_cli->estado = 1;
            $r_usu_cli->tipo_frecuencia = 0;
            $r_usu_cli->token = getToken();
            $r_usu_cli->_save(false, true);

        }

    }

}

?>