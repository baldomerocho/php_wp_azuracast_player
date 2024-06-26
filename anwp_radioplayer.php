<?php
/*
Plugin Name: AN Radio Player
Description: Un reproductor de radio sencillo con nombre de estación y pista en reproducción.
Version: 1.1
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

	add_settings_section('anwp_radioplayer_section', 'Radio Settings', null, 'anwp_radioplayer');

	add_settings_field('anwp_radio_url', 'Radio URL', 'anwp_radioplayer_url_callback', 'anwp_radioplayer', 'anwp_radioplayer_section');
	add_settings_field('anwp_radio_station_name', 'Radio Station Name', 'anwp_radioplayer_station_name_callback', 'anwp_radioplayer', 'anwp_radioplayer_section');
	add_settings_field('anwp_radio_metadata_url', 'Radio Metadata API URL', 'anwp_radioplayer_metadata_url_callback', 'anwp_radioplayer', 'anwp_radioplayer_section'); // Campo para la URL de la API de metadatos
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

// Encolar estilos y scripts
add_action('wp_enqueue_scripts', 'anwp_radioplayer_enqueue_scripts');

function anwp_radioplayer_enqueue_scripts() {
	wp_enqueue_style('anwp_radioplayer_styles', plugins_url('anwp_radioplayer.css', __FILE__));
	wp_enqueue_script('anwp_radioplayer_script', plugins_url('anwp_radioplayer.js', __FILE__), array('jquery'), null, true);
}

// Añadir el reproductor al pie de página
add_action('wp_footer', 'anwp_radioplayer_add_to_footer');
function anwp_radioplayer_add_to_footer() {
	$radio_url = esc_attr(get_option('anwp_radio_url'));
	$radio_station_name = esc_attr(get_option('anwp_radio_station_name'));
	$radio_metadata_url = esc_attr(get_option('anwp_radio_metadata_url')); // Añade la URL de la API de metadatos
	?>
    <div class="anwp-radio-player" id="anwp-radio-player">
        <h3><?php echo $radio_station_name; ?></h3>
        <audio controls>
            <source src="<?php echo $radio_url; ?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
        <p id="anwp-current-track"></p>
        <button class="close-button"></button>
    </div>
    <script>
        jQuery(document).ready(function($) {
            function updateTrackInfo() {
                $.get('<?php echo $radio_metadata_url; ?>', function(data) {
                    if (data && data.track && data.artist) {
                        $('#anwp-current-track').text(data.track + ' - ' + data.artist);
                    } else {
                        $('#anwp-current-track').text('Información de la pista no disponible');
                    }
                });
            }

            // Actualizar la pista en reproducción cada 30 segundos
            setInterval(updateTrackInfo, 30000);
            updateTrackInfo(); // Llamar a la función inmediatamente

            // Código JavaScript para cerrar el reproductor
            document.querySelector('.anwp-radio-player .close-button').addEventListener('click', function() {
                document.getElementById('anwp-radio-player').style.display = 'none';
            });
        });
    </script>
	<?php
}

?>
