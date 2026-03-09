$(document).ready(function() {

    $('#btnReset').click(function(e) {
        e.preventDefault();

        // Ocultar todas las alertas de inicio
        $('#emailAlert, #newPasswordAlert, #confirmPasswordAlert').hide();

        const email = $('#inputEmail').val().trim();
        const newPassword = $('#inputNewPassword').val().trim();
        const confirmPassword = $('#inputConfirmPassword').val().trim();

        // Si la validación pasa, procesamos el cambio
        if (validateFields(email, newPassword, confirmPassword)) {
            processForget(email, newPassword);
        }
    });

    /**
     * Valida los campos usando retornos tempranos.
     * Esto reduce la complejidad ciclomática drásticamente.
     */
    function validateFields(email, newPassword, confirmPassword) {
        if (email === "") {
            return showAlert('#emailAlert', "El campo Correo no puede estar vacío.");
        }

        if (newPassword === "") {
            return showAlert('#newPasswordAlert', "La Contraseña no puede estar vacía.");
        }

        if (confirmPassword === "") {
            return showAlert('#confirmPasswordAlert', "Confirma la contraseña.");
        }

        if (newPassword !== confirmPassword) {
            return showAlert('#confirmPasswordAlert', "Las contraseñas no coinciden.");
        }

        return true; // Todo correcto
    }

    // Función auxiliar para mostrar alertas y retornar false (ahorra líneas y complejidad)
    function showAlert(selector, mensaje) {
        $(selector).text(mensaje).show();
        return false;
    }

    /**
     * Procesa la petición AJAX.
     */
    function processForget(email, newPassword) {
        const url = 'http://localhost/sim/user/resetPassword';
        
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                'email': email,
                'new_password': newPassword
            },
            success: function(data) {
                handleResponse(data);
            },
            error: function() {
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    }

    /**
     * Maneja la respuesta de SweetAlert (separado para bajar complejidad)
     */
    function handleResponse(data) {
        if (data.status == 1) {
            return Swal.fire({
                title: '¡Éxito!',
                text: 'Tu contraseña ha sido restablecida correctamente.',
                icon: 'success',
                confirmButtonText: 'Iniciar Sesión'
            });
        }

        const config = data.status == 0 
            ? { title: 'Error de Restablecimiento', text: 'El correo no está registrado.', icon: 'error' }
            : { title: 'Error', text: 'Error inesperado en el servidor.', icon: 'warning' };

        Swal.fire({ ...config, confirmButtonText: 'Aceptar' });
    }
});
