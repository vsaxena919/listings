<?php
/**
 * Dynamic css generation file
 *
 */
?>
<?php
 $absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
 $wp_load = $absolute_path[0] . 'wp-load.php';
 require_once($wp_load);

header('Content-type: text/css');
header('Cache-control: must-revalidate');
header( "Content-type: text/css; charset: UTF-8" );

echo LP_dynamic_options_v2();
echo listingpro_dynamic_options();
echo listingpro_dynamic_css_options();

?>