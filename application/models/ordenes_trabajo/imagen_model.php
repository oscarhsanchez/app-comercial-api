<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_IMAGEN_ORDEN_TRABAJO);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "imagenes"
 * @Entity "ImagenOrden"
 * @Country true
 * @Autoincrement true;
 *
 */
class imagen_model extends generic_Model {

    function createImagen($entity, $array, $countryId) {

        if ($entity) {
            if (!$entity->data)
                throw new APIexception("Missing mandatory parameter", INVALID_NUMBER_OF_PARAMS, "");

            $filedata = $entity->data;
            unset($entity->data);

            $arrayFile = explode('.', $entity->nombre);
            $extension = end($arrayFile);

            if (isset($entity->token))
                $dbEntity = $this->getByToken($entity->token, $countryId);

            if ($dbEntity) {
                $entity->pk_archivo = $dbEntity->pk_archivo;
                $entity->nombre = $dbEntity->nombre;
                $entity->url = $dbEntity->url;
                $entity->path = $dbEntity->path;
            } else {
                $entity->nombre = getToken() . "." . $extension;
                $entity->url = ORDENES_IMAGES_URL . "/";
                $entity->path = ORDENES_IMAGES_PATH . "/";
                $entity->estado = 1;
                if (!$entity->estado_imagen) $entity->estado_imagen = 0;
            }

            file_put_contents($entity->path . $entity->nombre, $filedata);


        } else {

            foreach ($array as $entity) {
                if (!$entity->data)
                    throw new APIexception("Missing mandatory parameter", INVALID_NUMBER_OF_PARAMS, "");

                $filedata = $entity->data;
                unset($entity->data);

                $arrayFile = explode('.', $entity->nombre);
                $extension = end($arrayFile);

                if (isset($entity->token))
                    $dbEntity = $this->getByToken($entity->token, $countryId);

                if ($dbEntity) {
                    $entity->pk_archivo = $dbEntity->pk_archivo;
                    $entity->nombre = $dbEntity->nombre;
                    $entity->url = $dbEntity->url;
                    $entity->path = $dbEntity->path;
                } else {
                    $entity->nombre = getToken() . "." . $extension;
                    $entity->url = ORDENES_IMAGES_URL . "/";
                    $entity->path = ORDENES_IMAGES_PATH . "/";
                    $entity->estado = 1;
                    if (!$entity->estado_imagen) $entity->estado_imagen = 0;
                }

                file_put_contents($entity->path . $entity->nombre, $filedata);
            }


        }

        return $this->create($entity, $array, $countryId);

    }
	
}

?>