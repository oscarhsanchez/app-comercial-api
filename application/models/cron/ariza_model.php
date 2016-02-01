<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class ariza_model extends CI_Model {

    /**
     * Actualiza los precio minimos con el valor obtenido de coste_medio de la tabla r_art_alm
     */
    function updatePrecioMinimos() {


        $query = "UPDATE articulos art
                    JOIN r_art_alm rart ON art.fk_entidad = rart.fk_entidad AND art.pk_articulo = rart.fk_articulo
                    SET bool_modif_tarifa_desc = 1, precio_venta_minimo = coste_medio, descuento_maximo = ROUND(100 - (100*coste_medio/precio_venta), 2)
                    WHERE art.fk_entidad = 53 AND rart.coste_medio IS NOT NULL AND coste_medio > 0 AND coste_medio <> precio_venta_minimo";

        $query = $this->db->query($query);

        $query = "UPDATE articulos  SET precio_venta_minimo = 0 WHERE fk_entidad = 53 AND precio_venta_minimo IS NULL";

        $query = $this->db->query($query);

        $query = "UPDATE articulos  SET descuento_maximo = 100 WHERE fk_entidad = 53 AND descuento_maximo IS NULL";

        $query = $this->db->query($query);


    }

}

?>