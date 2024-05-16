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