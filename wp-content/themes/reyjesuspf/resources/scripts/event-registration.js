jQuery(document).ready(function ($) {
    $('#event-registration-form').on('submit', function (e) {
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
        // 1. Ejecutamos reCAPTCHA para obtener el token
        grecaptcha.ready(function () {
            // REEMPLAZA 'TU_CLAVE_PUBLICA_DE_SITIO' con tu clave real
            grecaptcha.execute('6LfflFgsAAAAAOYKX6iPoJkKCVJNWiN5fq7vQdsj', { action: 'register_to_event' }).then(function (token) {

                const eventId = $('input[name="event_id"]').val();
                const nombre = $('input[name="nombre"]').val();
                const cedula = $('input[name="cedula"]').val();
                const email = $('input[name="email"]').val();
                const iglesia = $('select[name="iglesia"]').val();
                const red = $('select[name="red"]').val();
                const phoneNumber = $('input[name="phone_number"]').val();
                // Datos a enviar a la función de PHP a través de AJAX
                var data = {
                    action: 'register_to_event',
                    event_id: eventId,
                    nombre: nombre,
                    cedula: cedula,
                    email: email,
                    iglesia: iglesia,
                    red: red,
                    phone_number: phoneNumber,
                    nonce: event_reg_ajax_obj.nonce,
                    // 2. Añadimos el token de reCAPTCHA a los datos
                    recaptcha_response: token,
                };

                $.ajax({
                    type: 'POST',
                    url: event_reg_ajax_obj.ajax_url,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            messagesDiv.text(response.data.message).addClass('text-green-600');
                            form[0].reset(); // Reseteamos el formulario
                        } else {
                            messagesDiv.text(response.data.message).addClass('text-red-600');
                        }
                    },
                    error: function () {
                        messagesDiv.text('Error de conexión. Por favor, inténtalo de nuevo.').addClass('text-red-600');
                    },
                    complete: function () {
                        submitButton.prop('disabled', false);
                        buttonText.removeClass('hidden');
                        buttonSpinner.addClass('hidden');
                    }
                });

            });
        });
    });
});