<?php
/*
Plugin Name: AN Radio Player
Description: Un reproductor de radio sencillo con nombre de estación y pista en reproducción.
Version: 1.2
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
	register_setting('anwp_radioplayer_settings_group', 'anwp_radio_metadata_url'); // Añade la URL de la API de metadatos
	register_setting('anwp_radioplayer_settings_group', 'anwp_radio_apikey'); // Añade el campo para el API Key

	add_settings_section('anwp_radioplayer_section', 'Radio Settings', null, 'anwp_radioplayer');

	add_settings_field('anwp_radio_url', 'Radio URL', 'anwp_radioplayer_url_callback', 'anwp_radioplayer', 'anwp_radioplayer_section');
	add_settings_field('anwp_radio_station_name', 'Radio Station Name', 'anwp_radioplayer_station_name_callback', 'anwp_radioplayer', 'anwp_radioplayer_section');
	add_settings_field('anwp_radio_metadata_url', 'Radio Metadata API URL', 'anwp_radioplayer_metadata_url_callback', 'anwp_radioplayer', 'anwp_radioplayer_section'); // Campo para la URL de la API de metadatos
	add_settings_field('anwp_radio_apikey', 'API Key', 'anwp_radioplayer_apikey_callback', 'anwp_radioplayer', 'anwp_radioplayer_section'); // Campo para el API Key
}

function anwp_radioplayer_metadata_url_callback() {
	$radio_metadata_url = esc_attr(get_option('anwp_radio_metadata_url'));
	echo '<input type="text" name="anwp_radio_metadata_url" value="' . $radio_metadata_url . '" class="regular-text" />';
}

function anwp_radioplayer_url_callback() {
	$radio_url = esc_attr(get_option('anwp_radio_url'));
	echo '<input type="text" name="anwp_radio_url" value="' . $radio_url . '" class="regular-text" />';
}

function anwp_radioplayer_station_name_callback() {
	$radio_station_name = esc_attr(get_option('anwp_radio_station_name'));
	echo '<input type="text" name="anwp_radio_station_name" value="' . $radio_station_name . '" class="regular-text" />';
}

function anwp_radioplayer_apikey_callback() {
	$radio_apikey = esc_attr(get_option('anwp_radio_apikey'));
	echo '<input type="text" name="anwp_radio_apikey" value="' . $radio_apikey . '" class="regular-text" />';
}

// Realizar la solicitud a la API desde PHP
function anwp_radioplayer_fetch_nowplaying() {
	$radio_metadata_url = esc_attr(get_option('anwp_radio_metadata_url'));
	$radio_apikey = esc_attr(get_option('anwp_radio_apikey'));

	// Configurar los headers para la solicitud
	$args = array(
		'headers' => array(
			'Accept'        => 'application/json',
			'Authorization' => 'Bearer ' . $radio_apikey
		)
	);

	// Realizar la solicitud a la API
	$response = wp_remote_get($radio_metadata_url . '/api/nowplaying', $args);

	if (is_wp_error($response)) {
		return json_encode(['error' => 'No se pudo obtener la información']);
	}

	$body = wp_remote_retrieve_body($response);
	return $body;
}

// Crear el endpoint para AJAX
add_action('wp_ajax_anwp_get_nowplaying', 'anwp_radioplayer_ajax_handler');
add_action('wp_ajax_nopriv_anwp_get_nowplaying', 'anwp_radioplayer_ajax_handler');

function anwp_radioplayer_ajax_handler() {
	$data = anwp_radioplayer_fetch_nowplaying();
	wp_send_json(json_decode($data, true)); // Enviar los datos al frontend en formato JSON
}

// Encolar estilos y scripts
add_action('wp_enqueue_scripts', 'anwp_radioplayer_enqueue_scripts');

function anwp_radioplayer_enqueue_scripts() {
	wp_enqueue_style('anwp_radioplayer_styles', plugins_url('anwp_radioplayer.css', __FILE__));
	wp_enqueue_script('anwp_radioplayer_script', plugins_url('anwp_radioplayer.js', __FILE__), array('jquery'), null, true);

	// Localizar el script para usar la URL del AJAX
	wp_localize_script('anwp_radioplayer_script', 'anwpRadioPlayer', array(
		'ajaxurl' => admin_url('admin-ajax.php')
	));
}

// Añadir el reproductor al pie de página
add_action('wp_footer', 'anwp_radioplayer_add_to_footer');
function anwp_radioplayer_add_to_footer() {
	?>
    <div class="anwp-radio-player" id="anwp-radio-player">
        <h3 id="anwp-radio-station"></h3>
        <div class="anwp-radio-meta">
            <p id="anwp-current-track"></p>
        </div>
        <div class="anwp-audio-player">
            <audio id="anwp-audio-player" controls>
                <source id="anwp-audio-source" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </div>
    </div>

	<?php
}
?>
