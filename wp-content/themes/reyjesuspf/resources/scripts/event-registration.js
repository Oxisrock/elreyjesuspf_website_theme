jQuery(document).ready(function($) {
    $('#event-registration-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var messagesDiv = $('#form-messages');
        var buttonText = submitButton.find('.button-text');
        var buttonSpinner = submitButton.find('.button-spinner');

        submitButton.prop('disabled', true);
        buttonText.addClass('hidden');
        buttonSpinner.removeClass('hidden');
        messagesDiv.text('').removeClass('success error');

        const eventId = $('input[name="event_id"]').val();
        const nombre = $('input[name="nombre"]').val();
        const email = $('input[name="email"]').val();
        const phoneNumber = $('input[name="phone_number"]').val();
        // Datos a enviar a la función de PHP a través de AJAX
       var data = {
            action: 'register_to_event', // Esta es la acción de AJAX definida en tu PHP
            event_id: eventId,
            nombre: nombre,
            email: email,
            phone_number: phoneNumber, // Añade el número de teléfono aquí
            nonce: event_reg_ajax_obj.nonce // Nonce de seguridad
        };

        $.ajax({
            type: 'POST',
            url: event_reg_ajax_obj.ajax_url,
            data: data,
            success: function(response) {
                if (response.success) {
                    messagesDiv.text(response.data.message).addClass('text-green-600');
                    form[0].reset(); // Reseteamos el formulario
                } else {
                    messagesDiv.text(response.data.message).addClass('text-red-600');
                }
            },
            error: function() {
                messagesDiv.text('Error de conexión. Por favor, inténtalo de nuevo.').addClass('text-red-600');
            },
            complete: function() {
                submitButton.prop('disabled', false);
                buttonText.removeClass('hidden');
                buttonSpinner.addClass('hidden');
            }
        });
    });
});