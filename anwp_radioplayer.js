jQuery(document).ready(function($) {
    let audioInitialized = false; // Variable para asegurarnos de no inicializar el audio varias veces

    function updateTrackInfo() {
        $.ajax({
            url: anwpRadioPlayer.ajaxurl,
            method: 'POST',
            data: {
                action: 'anwp_get_nowplaying'
            },
            success: function(response) {
                if (response && response[0]) {
                    const stationData = response[0];

                    // Actualizar información de la estación (solo texto)
                    $('#anwp-radio-station').text(stationData.station.name);

                    // Si el audio no ha sido inicializado, establecer la fuente del audio
                    if (!audioInitialized) {
                        $('#anwp-audio-source').attr('src', stationData.station.listen_url);
                        document.getElementById('anwp-audio-player').load();  // Inicializar el reproductor de audio
                        audioInitialized = true; // Evitar que se vuelva a inicializar
                    }

                    // Actualizar información de la pista (debajo del título de la estación)
                    if (stationData.now_playing && stationData.now_playing.song) {
                        $('#anwp-current-track').html(
                            '<strong>Canción actual:</strong><br>' +
                            stationData.now_playing.song.artist + ' - ' +
                            stationData.now_playing.song.title
                        );
                    } else {
                        $('#anwp-current-track').text('No hay información disponible sobre la pista actual');
                    }
                } else {
                    $('#anwp-current-track').text('No se pudieron obtener datos de la API');
                }
            },
            error: function() {
                $('#anwp-current-track').text('Error al obtener datos del servidor');
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
