<?php
/**
 * solr.php
 *
 * @author Simon Emms <simon@bigeyedeers.co.uk>
 */

/* Basic connection details */
$config['solr_hostname'] = 'localhost';
$config['solr_port'] = '8983';
$config['solr_path'] = '/solr';

/* Other config */
$config['solr_config'] = array(
    'show_errors' => false,          // Do we exit on errors?
	'table' => 'type',				// The column that is the "table" name
);

?>