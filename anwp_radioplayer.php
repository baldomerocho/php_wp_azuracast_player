<?php
/*
Plugin Name: AN Radio Player
Description: Un reproductor de radio sencillo con nombre de estación y pista en reproducción.
Version: 1.0
Author: Tu Nombre
*/

if (!defined('ABSPATH')) {
	exit; // Salir si se accede directamente.
}

// Registrar el menú de administración
add_action('admin_menu', 'anwp_radioplayer_create_menu');

function anwp_radioplayer_create_menu() {
	add_menu_page(
		'AN Radio Player Settings',
		'AN Radio Player',
		'manage_options',
		'anwp_radioplayer',
		'anwp_radioplayer_settings_page',
		'dashicons-admin-generic'
	);
}

// Crear la página de configuración
function anwp_radioplayer_settings_page() {
	?>
	<div class="wrap">
		<h1>AN Radio Player Settings</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields('anwp_radioplayer_settings_group');
			do_settings_sections('anwp_radioplayer');
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// Registrar las configuraciones
add_action('admin_init', 'anwp_radioplayer_register_settings');

function anwp_radioplayer_register_settings() {
	register_setting('anwp_radioplayer_settings_group', 'anwp_radio_url');
	register_setting('anwp_radioplayer_settings_group', 'anwp_radio_station_name');

	add_settings_section('anwp_radioplayer_section', 'Radio Settings', null, 'anwp_radioplayer');

	add_settings_field('anwp_radio_url', 'Radio URL', 'anwp_radioplayer_url_callback', 'anwp_radioplayer', 'anwp_radioplayer_section');
	add_settings_field('anwp_radio_station_name', 'Radio Station Name', 'anwp_radioplayer_station_name_callback', 'anwp_radioplayer', 'anwp_radioplayer_section');
}

function anwp_radioplayer_url_callback() {
	$radio_url = esc_attr(get_option('anwp_radio_url'));
	echo '<input type="text" name="anwp_radio_url" value="' . $radio_url . '" class="regular-text" />';
}

function anwp_radioplayer_station_name_callback() {
	$radio_station_name = esc_attr(get_option('anwp_radio_station_name'));
	echo '<input type="text" name="anwp_radio_station_name" value="' . $radio_station_name . '" class="regular-text" />';
}

// Shortcode para mostrar el reproductor de radio
function anwp_radioplayer_shortcode() {
	$radio_url = esc_attr(get_option('anwp_radio_url'));
	$radio_station_name = esc_attr(get_option('anwp_radio_station_name'));

	ob_start();
	?>
	<div class="anwp-radio-player">
		<h3><?php echo $radio_station_name; ?></h3>
		<audio controls>
			<source src="<?php echo $radio_url; ?>" type="audio/mpeg">
			Your browser does not support the audio element.
		</audio>
		<p id="anwp-current-track"></p>
	</div>
	<script>
        // Código JavaScript para actualizar el nombre de la pista en reproducción
        // Aquí puedes añadir el código necesario para obtener y mostrar el nombre de la pista en reproducción
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode('anwp_radioplayer', 'anwp_radioplayer_shortcode');
