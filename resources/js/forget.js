$(document).ready(function() {
    $('#btnReset').click(function(e) {
        e.preventDefault();
        $('.alert').hide();

        const email = $('#inputEmail').val().trim();
        const pass = $('#inputNewPassword').val().trim();
        const conf = $('#inputConfirmPassword').val().trim();

        // Validación directa sin funciones extra
        if (!email || !pass || !conf) {
            return Swal.fire('Error', 'Todos los campos son obligatorios', 'warning');
        }

        if (pass !== conf) {
            return $('#confirmPasswordAlert').text("Las contraseñas no coinciden.").show();
        }

        $.post('http://localhost/sim/user/resetPassword', { email, new_password: pass }, function(data) {
            const icon = data.status == 1 ? 'success' : 'error';
            Swal.fire(data.status == 1 ? '¡Éxito!' : 'Error', data.message, icon);
        }, 'json').fail(() => Swal.fire('Error', 'Fallo de conexión', 'error'));
    });
});
