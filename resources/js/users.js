$(document).ready(function() {

    // 1. Manejador del evento de clic en el botón de Login
    $('#btnLogin').click(function(e) {
        e.preventDefault(); // Previene comportamientos extraños
        
        // Ocultar alertas previas
        $('#MatriculaAlert, #passwordAlert').hide();

        const matricula = $('#inputMatricula').val().trim();
        const password = $('#password').val().trim();

        // Validar y procesar
        if (validateFields(matricula, password)) {
            processLogin(matricula, password);
        }
    });

    /**
     * Valida que los campos no estén vacíos.
     * Reducción de complejidad: Se eliminó el uso de variables de estado (status).
     */
    function validateFields(matricula, password) {
        if (matricula === "") {
            $('#MatriculaAlert').text("El campo Matrícula no puede estar vacío.").show();
            return false; // Corta la ejecución de inmediato
        }
        
        if (password === "") {
            $('#passwordAlert').text("El campo Contraseña no puede estar vacío.").show();
            return false; // Corta la ejecución de inmediato
        }

        return true; 
    }

    /**
     * Realiza la llamada AJAX al controlador para validar las credenciales.
     */
    function processLogin(matricula, password) {
        const url = 'http://localhost/UniCham/login/validador';

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                'matricula': matricula,
                'password': password
            },
            success: function(response) {
                manejarRespuestaLogin(response);
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                mostrarAlerta('Error de Conexión', 'No se pudo conectar con el servidor.', 'error');
            }
        });
    }

    /**
     * Función auxiliar para procesar la respuesta del servidor (Baja la complejidad de la función principal)
     */
    function manejarRespuestaLogin(response) {
        if (!response || typeof response.status === 'undefined') {
            mostrarAlerta('Error', 'Respuesta inválida del servidor.', 'error');
            return;
        }

        const { status, message, redirect_url } = response;
        const targetUrl = redirect_url || 'http://localhost/UniCham/user/perfil';

        if (status === 1) {
            Swal.fire({
                title: 'Acceso Satisfactorio',
                text: 'Redirigiendo al sistema...',
                icon: 'success',
                confirmButtonText: 'Continuar'
            }).then(() => {
                window.location.href = targetUrl;
            });
        } else {
            mostrarAlerta('Error de Login', message || 'Error en la validación.', status === 0 ? 'error' : 'warning');
        }
    }

    // Función genérica para mostrar alertas de SweetAlert2
    function mostrarAlerta(titulo, texto, icono) {
        Swal.fire({
            title: titulo,
            text: texto,
            icon: icono,
            confirmButtonText: 'Cerrar'
        });
    }
});
