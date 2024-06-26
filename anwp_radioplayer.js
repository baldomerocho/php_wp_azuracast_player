jQuery(document).ready(function($) {
    // Función para obtener y mostrar el nombre de la pista en reproducción
    function updateTrackInfo() {
        // Lógica para obtener el nombre de la pista en reproducción desde la API del proveedor
        // Por ejemplo:
        // $.get('URL_DE_LA_API', function(data) {
        //     $('#anwp-current-track').text(data.trackName);
        // });
    }

    // Actualizar la pista en reproducción cada 30 segundos
    setInterval(updateTrackInfo, 30000);
    updateTrackInfo(); // Llamar a la función inmediatamente
});