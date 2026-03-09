$(document).ready(function() {
    $('#btnRegister').click(function(e) {
        e.preventDefault();
        $('.alert').hide();

        const data = {
            name: $('#inputName').val().trim(),
            firstname: $('#inputFirstname').val().trim(),
            lastname: $('#inputLastname').val().trim(),
            phone: $('#inputPhone').val().trim(),
            password: $('#inputPassword').val().trim(),
            confirm: $('#inputConfirmPassword').val().trim()
        };

        if (validate(data)) {
            $.post('/UniCham/index.php?controller=user&method=registro', data, function(res) {
                const icon = res.status == 1 ? 'success' : 'error';
                const title = res.status == 1 ? '¡Éxito!' : 'Error';
                Swal.fire(title, res.message || (res.status == 1 ? 'Cuenta creada.' : 'Error al registrar.'), icon);
            }, 'json').fail(() => Swal.fire('Error', 'Fallo de conexión.', 'error'));
        }
    });

    function validate(d) {
        if (!d.name || !d.firstname || !d.lastname || !d.phone || !d.password) {
            Swal.fire('Error', 'Todos los campos son obligatorios', 'warning');
            return false;
        }
        if (d.password !== d.confirm) {
            $('#confirmPasswordAlert').text("Las contraseñas no coinciden.").show();
            return false;
        }
        return true;
    }
});
