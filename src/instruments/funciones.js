function validatePassword(contraseña, contraseña2, errorContainer) {
    const PASSWORD_REGEX = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;

    if (!PASSWORD_REGEX.test(contraseña) || !PASSWORD_REGEX.test(contraseña2)) {
        errorContainer.html('<p>La contraseña debe contener al menos una letra, mayúscula y minúcula, un número, y tener al menos 8 caracteres</p>');
        return false;
    } else if (!contraseña || !contraseña2) {
        errorContainer.html('<p>Deben completarse ambos campos</p>');
        return false;
    } else if (contraseña !== contraseña2) {
        errorContainer.html('<p>Las contraseñas no coinciden</p>');
        return false;
    } else {
        errorContainer.html('');
        return true;
    }
}

function getCookie(name) {
    let cookieArr = document.cookie.split(";");
    for (let i = 0; i < cookieArr.length; i++) {
        let cookiePair = cookieArr[i].split("=");
        if (name === cookiePair[0].trim()) {
            return decodeURIComponent(cookiePair[1]);
        }
    }
    return null;
}

function showPopupMessage(message) {
    let overlay = $('.popup-overlay');
    if (overlay.length === 0) {
        overlay = $('<div class="popup-overlay"></div>');
        $('body').append(overlay);
    }
    let popup = $('.popup-message');
    if (popup.length === 0) {
        popup = $('<div class="popup-message"></div>');
        $('body').append(popup);
    }
    popup.text(message);
    overlay.fadeIn(300);
    popup.css({ left: '-50%' }).show().animate({ left: '50%' }, 500);
    overlay.on('click', () => {
        popup.animate({ left: '-50%' }, 500, function () {
            $(this).hide();
        });
        overlay.fadeOut(300);
    });
}

 function actualizarCuota(switchChange, isManualChange) {
            let row = switchChange.closest('tr');
            let socioUsuario = row.getAttribute('data-id');
            let cuotaPagada = switchChange.checked ? 1 : 0;
            let fechaUltimoPago = null;
            let fechaProximoPago = null;

            if (cuotaPagada) {
                let now = new Date();
                fechaUltimoPago = now.toISOString().split('T')[0];

                let nextMonth = new Date(now.setMonth(now.getMonth() + 1));
                let nextYear = nextMonth.getFullYear();
                let nextMonthNumber = nextMonth.getMonth() + 1;
                let nextDay = nextMonth.getDate();

                fechaProximoPago = new Date(nextYear, nextMonthNumber - 1, nextDay).toISOString().split('T')[0];
            }

            fetch('../instruments/actualizar_cuota_Socio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    socioUsuario: socioUsuario,
                    cuotaPagada: cuotaPagada,
                    fechaUltimoPago: isManualChange && cuotaPagada ? fechaUltimoPago : null,
                    fechaProximoPago: isManualChange && cuotaPagada ? fechaProximoPago : null
                }).toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (cuotaPagada) {
                        row.querySelector('.fecha-ultimo-pago').textContent = formatDate(fechaUltimoPago);
                        row.querySelector('.fecha-proximo-pago').textContent = formatDate(fechaProximoPago);
                    }
                } else {
                    alert('Error al actualizar la cuota');
                }
            });
        }

        function formatDate(dateString) {
            let [year, month, day] = dateString.split('-');
            return `${day}-${month}-${year}`;
        }