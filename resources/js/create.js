$(document).ready(function() {
    $('#btnRegister').click(function(e) {
        e.preventDefault();
        $('.alert').hide(); // Oculta todas las alertas de un golpe

        const data = {
            name: $('#inputName').val().trim(),
            firstname: $('#inputFirstname').val().trim(),
            lastname: $('#inputLastname').val().trim(),
            phone: $('#inputPhone').val().trim(),
            password: $('#inputPassword').val().trim()
        };
        const confirm = $('#inputConfirmPassword').val().trim();

        if (validateFields(data, confirm)) {
            processRegistration(data);
        }
    });

    function validateFields(d, confirm) {
        if (!d.name) return showErr('#nameAlert', "Campo obligatorio");
        if (!d.firstname) return showErr('#firstnameAlert', "Campo obligatorio");
        if (!d.lastname) return showErr('#lastnameAlert', "Campo obligatorio");
        if (!d.phone) return showErr('#phoneAlert', "Campo obligatorio");
        if (!d.password) return showErr('#passwordAlert', "Campo obligatorio");
        if (d.password !== confirm) return showErr('#confirmPasswordAlert', "No coinciden");
        return true;
    }

    function showErr(id, msg) {
        $(id).text(msg).show();
        return false;
    }

    function processRegistration(info) {
        $.post('/UniCham/index.php?controller=user&method=registro', info, function(res) {
            if (res.status == 1) {
                return Swal.fire({ title: '¡Exitoso!', text: 'Cuenta creada.', icon: 'success' });
            }
            const msg = res.status == 0 ? 'El teléfono ya existe.' : 'Error inesperado.';
            Swal.fire('Error', msg, 'error');
        }, 'json').fail(() => Swal.fire('Error', 'Fallo de conexión', 'error'));
    }
});
