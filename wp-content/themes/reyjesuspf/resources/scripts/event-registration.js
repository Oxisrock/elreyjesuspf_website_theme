// Usamos jQuery que ya viene con WordPress
jQuery(document).ready(function($) {
    // 1. Escuchar el evento 'submit' en nuestro formulario
    $('#event-registration-form').on('submit', function(e) {
        e.preventDefault(); // Prevenimos el comportamiento por defecto del formulario

        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var messagesDiv = $('#form-messages');
        var emailInput = form.find('input[name="email"]');

        // Deshabilitamos el botón para evitar envíos múltiples
        submitButton.prop('disabled', true).text('Enviando...');
        messagesDiv.text('').removeClass('success error'); // Limpiamos mensajes anteriores

        // 2. Recopilamos los datos del formulario
        var formData = {
            action: 'register_to_event', // Esta es la acción que buscará WordPress en el backend
            nonce: event_reg_ajax_obj.nonce, // Nonce de seguridad que pasamos desde PHP
            email: emailInput.val(),
            event_id: form.find('input[name="event_id"]').val()
        };

        // 3. Enviamos los datos usando AJAX
        $.ajax({
            type: 'POST',
            url: event_reg_ajax_obj.ajax_url, // La URL del admin-ajax.php que pasamos desde PHP
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Si el registro fue exitoso
                    messagesDiv.text(response.data.message).addClass('text-green-600');
                    emailInput.val(''); // Limpiamos el campo de email
                } else {
                    // Si hubo un error
                    messagesDiv.text(response.data.message).addClass('text-red-600');
                }
            },
            error: function() {
                // En caso de un error de conexión
                messagesDiv.text('Error de conexión. Por favor, inténtalo de nuevo.').addClass('text-red-600');
            },
            complete: function() {
                // Cuando la petición termina, reactivamos el botón
                submitButton.prop('disabled', false).text('Enviar');
            }
        });
    });
});
