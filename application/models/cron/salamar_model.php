<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class salamar_model extends CI_Model {


    /**
     * Actualiza los precio minimos con el valor obtenido de coste_medio de la tabla r_art_alm
     */
    function updateStockRepartidores() {

        $almacenes=array('69_27', 'A000000017_27', 'A000000018_27', '63_27', '61_27', 'A000000015_27', 'A000000011_27', 'A000000014_27', '71_27', 'A000000019_27');

        foreach ($almacenes as $pkAlma) {

            $query = "INSERT INTO r_art_alm (fk_entidad, fk_almacen, fk_articulo, stock_min, stock_max, unidades, token)
                        SELECT 27, '".$pkAlma."', fk_articulo, 0, 0, 500, SHA1(CONCAT(RAND(),UNIX_TIMESTAMP(),RAND())) FROM r_art_alm WHERE fk_entidad = 27 AND fk_almacen = '1_27' AND fk_articulo NOT IN (
                        SELECT fk_articulo FROM r_art_alm WHERE fk_entidad = 27 AND fk_almacen = '".$pkAlma."'
                        )";

            $query = $this->db->query($query);

            $query = "UPDATE r_art_alm SET unidades = 500 WHERE fk_entidad = 27 AND fk_almacen = '".$pkAlma."'";

            $query = $this->db->query($query);
        }


    }

    /**
     * Eliminamos los cobros repetidos que se generan desde el AS/400
     */
    function deleteCobrosRepetidos() {


            $query = "UPDATE recibos_cobro SET estado = 0 WHERE fk_entidad = 27 AND estado_recibo = 1 AND fk_forma_pago IS NULL AND fk_factura_cliente IN (
                        SELECT fk_factura_cliente FROM facturas_cab cab
                        JOIN (
                            SELECT fk_factura_cliente, SUM(total) AS tot_cobros FROM recibos_cobro WHERE fk_entidad = 27 AND estado > 0 GROUP BY fk_factura_cliente) cob  ON pk_factura = fk_factura_cliente
                            WHERE cab.fk_entidad = 27 AND cab.estado > 0 AND estado_factura < 2 AND  ABS(tot_cobros) > ABS(imp_total*2)-0.5
                        ) "."";

            $query = $this->db->query($query);



    }

    /**
     * Actualiza los precios minimos en base al descuento por si hay modificaciones de precio
     */
    function updatePreciosMinimos() {

        //Actualizamos primero el descuento en caso de que exista un precio minimos con descuento = 0
        $query = "UPDATE articulos SET descuento_maximo = 100 - ROUND(precio_venta_minimo*100/precio_venta, 2) WHERE fk_entidad = 27 AND descuento_maximo = 0 AND precio_venta_minimo <> 0";

        $query = $this->db->query($query);

        //Actualizamos precios minimos
        $query = "UPDATE articulos SET precio_venta_minimo = ROUND(precio_venta - ROUND(precio_venta*descuento_maximo/100, 2), 2) WHERE fk_entidad = 27 AND descuento_maximo <> 0 AND ROUND(precio_venta - ROUND(precio_venta*descuento_maximo/100, 2), 2) <> precio_venta_minimo";

        $query = $this->db->query($query);



    }

    /**
     * Actualiza los precios minimos en base al descuento por si hay modificaciones de precio
     */
    function delFacturasSync($date) {


        $query = "UPDATE facturas_cab SET estado = 0 WHERE fk_entidad = 27 AND fecha = '$date' AND (varios1 != 'AS400' OR varios1 IS NULL)";

        $query = $this->db->query($query);


        $query = "UPDATE facturas_cab cab
                    JOIN facturas_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_factura= pk_factura AND lin.estado > 0
                    SET lin.estado = 0
                    WHERE cab.fk_entidad = 27 AND fecha = '$date' AND (lin.varios1 != 'AS400' OR lin.varios1 IS NULL)
                 ";

        $query = $this->db->query($query);



    }

}

?>