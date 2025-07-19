function confirmAction(event, form, actionType) {
    event.preventDefault(); // Previene el envío automático del formulario

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success swal-button-spacing",
            cancelButton: "btn btn-danger swal-button-spacing"
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: actionType === "activar" ? "¿Estás seguro de activar?" : "¿Estás seguro de desactivar?",
        text: actionType === "activar" ? "Será activado." : "Será desactivado.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: actionType === "activar" ? "Sí, activar" : "Sí, desactivar",
        cancelButtonText: "No, cancelar",
        reverseButtons: true,
        preConfirm: () => {
            form.submit(); // Enviar el formulario INMEDIATAMENTE
        }
    });
}

//Eliminar asistente desde editar evento alert
function confirmDelete(event, form) {
    event.preventDefault(); // Previene el envío automático del formulario

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-danger swal-button-spacing",
            cancelButton: "btn btn-secondary swal-button-spacing"
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "¿Estás seguro de eliminar?",
        text: "Esta acción no se podrá revertir.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar",
        reverseButtons: true,
        preConfirm: () => {
            form.submit();
        }
    });
}

//Eliminar asistente desde editar evento alert
function confirmAssist(event, form) {
    event.preventDefault(); // Previene el envío automático del formulario

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-danger swal-button-spacing",
            cancelButton: "btn btn-secondary swal-button-spacing"
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "¿Estás seguro de confirmar su asistencia?",
        text: "Esta acción no se podrá revertir.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No, cancelar",
        reverseButtons: true,
        preConfirm: () => {
            form.submit();
        }
    });
}





function to_money($number) {
    if ($number !== '') {
        $number = parseFloat($number);
        return '$ ' + $number.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,").replace(/,/g, ".");
    } else {
        return '$ 0';
    }
}

function to_number($number) {
    if ($number !== '') {
        $number = parseFloat($number);
        return '' + $number.toFixed().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,").replace(/,/g, ".");
    } else {
        return '0';
    }
}

const HTML_LOADING = ' < div className = "d-flex justify-content-center align-items-center" > < div className = "spinner-border text-primary" role = "status" style = "font-size: 1rem;" > < span className = "sr-only" > Cargando...</span></div></div>';

function ajaxRequest($type, $url, $div, $filters = {}, $toast = null, $callback = null) {

    var baseUrl = window.location.origin; // Get the base URL dynamically

    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });


    $.ajax({
        beforeSend: function () {
            $('#' + $div).html(HTML_LOADING);
        },
        url: baseUrl + '/' + $url, // Use the base URL dynamically
        type: $type,
        headers: {},
        data: $filters,
        cache: false,
        timeout: 120000,  // sets timeout to 10 seconds (adjust as necessary)
    }).done(function (response) {
        console.log(response);
        $('#' + $div).html(response);
        if ($toast) {
            Toast.fire({
                icon: 'success',
                title: $toast
            }).then((result) => {
                if ($callback && typeof $callback === "function") {
                    $callback();
                }
            })
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        var errorMessage = jqXHR.responseText ? jqXHR.responseText : 'Error en la solicitud';
        $('#' + $div).html(errorMessage);
        Toast.fire({
            icon: 'error',
            title: errorThrown || 'Error en la solicitud'
        });
    });
}



function ajaxRequestAlert(type, url, filters = {}, callback) {

    var baseUrl = window.location.origin;

    $.ajax({
        url: baseUrl + '/' + url, // Use the base URL dynamically
        beforeSend: function () {
            Swal.fire(
                {
                    title: 'Cargando..',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                }
            )
        },
        type: type,
        headers: {},
        data: filters,
        cache: false,
        timeout: 120000, // sets timeout to 30 seconds
    }).done(function (response) {
        Swal.fire(
            'Proceso Correcto',
            response.message,
            'success'
        ).then((result) => {
            if (callback && typeof callback === "function") {
                callback();
            }
        })
    }).fail(function (jqXHR, textStatus) {
        Swal.fire(
            'Error en el Proceso',
            jqXHR.responseText,
            'error'
        )
        console.log(textStatus);
    });

}

//Iframe individuales.
