$(document).ready(function() {
    $('#btnUpload').click(function(e) {
        e.preventDefault();
        $('.alert').hide();

        const title = $('#inputTitle').val().trim();
        const file = $('#inputFile')[0].files[0];

        if (!title) {
            return $('#titleAlert').text("El título es obligatorio.").show();
        }
        if (!file) {
            return $('#fileAlert').text("Selecciona un archivo.").show();
        }

        uploadFile(title, file);
    });

    function uploadFile(title, file) {
        const formData = new FormData();
        formData.append('title', title);
        formData.append('file', file);

        $.ajax({
            url: '/UniCham/index.php?controller=document&method=upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                const icon = res.status == 1 ? 'success' : 'error';
                Swal.fire(res.status == 1 ? '¡Éxito!' : 'Error', res.message, icon);
            },
            error: function() {
                Swal.fire('Error', 'Fallo en la conexión.', 'error');
            }
        });
    }
});
