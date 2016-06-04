<?php

if ($handle = opendir('/var/www/vhosts/vallas-admin/shared/web/media/ubicacion_imagen')) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {

            echo "$entry\n";
        }
    }

    closedir($handle);
}

?>