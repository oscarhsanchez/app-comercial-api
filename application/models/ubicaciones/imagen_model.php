<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_IMAGEN_UBICACION);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "imagenes_ubicaciones"
 * @Entity "ImagenUbicacion"
 * @Country true
 * @Autoincrement true;
 *
 */
class imagen_model extends generic_Model {

    function createImagen($entity, $array, $countryId) {
        $arrImg = array();
        if ($entity) {
            if (!$entity->data || !$entity->nombre)
                throw new APIexception("Missing mandatory parameter", INVALID_NUMBER_OF_PARAMS, "");

            $filedata = base64_decode($entity->data);
            unset($entity->data);

            $arrayFile = explode('.', $entity->nombre);
            $extension = end($arrayFile);

            $instance = new ImagenUbicacion();
            $instance->set($entity);

            $dbEntity = null;
            if (isset($entity->token))
                $dbEntity = $this->getByToken($entity->token, $countryId);

            if ($dbEntity) {
                $instance->pk_archivo = $dbEntity->pk_archivo;
                $instance->nombre = $dbEntity->nombre;
                $instance->url = $dbEntity->url;
                $instance->path = $dbEntity->path;
                $instance->fk_pais = $countryId;
            } else {
                //$instance->nombre = getToken() . "." . $extension;  Dejamos el nombre del archivo que viene.
                $instance->url = UBICACIONES_IMAGES_URL . "/";
                $instance->path = UBICACIONES_IMAGES_PATH . "/";
                $instance->estado = 1;
                $instance->fk_pais = $countryId;
                if (!$instance->estado_imagen) $instance->estado_imagen = 0;
            }

            file_put_contents($instance->path . $instance->nombre, $filedata);


        } else {

            foreach ($array as $entity) {
                if (!$entity->data || !$entity->nombre)
                    throw new APIexception("Missing mandatory parameter", INVALID_NUMBER_OF_PARAMS, "");

                $filedata = base64_decode($entity->data);
                unset($entity->data);

                $arrayFile = explode('.', $entity->nombre);
                $extension = end($arrayFile);

                $instance = new ImagenUbicacion();
                $instance->set($entity);

                $dbEntity = null;
                if (isset($entity->token))
                    $dbEntity = $this->getByToken($entity->token, $countryId);

                if ($dbEntity) {
                    $instance->pk_archivo = $dbEntity->pk_archivo;
                    $instance->nombre = $dbEntity->nombre;
                    $instance->url = $dbEntity->url;
                    $instance->path = $dbEntity->path;
                    $instance->fk_pais = $countryId;
                } else {
                    //$instance->nombre = getToken() . "." . $extension;
                    $instance->url = UBICACIONES_IMAGES_URL . "/";
                    $instance->path = UBICACIONES_IMAGES_PATH . "/";
                    $instance->estado = 1;
                    $instance->fk_pais = $countryId;
                    if (!$instance->estado_imagen) $instance->estado_imagen = 0;
                }

                $arrImg[] = $instance;

                file_put_contents($instance->path . $instance->nombre, $filedata);
            }


        }

        return $this->create($instance, $arrImg, $countryId);

    }
	
}

?>