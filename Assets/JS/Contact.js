/**
 * Checks if a value has been entered in a checkbox type input.
 * (PRIVATE)
 *
 * @param {String} id
 * @returns {Boolean}
 */
function checkCheckBox(id) {
    const input = document.querySelector('#' + id);
    if (input.indeterminate) {
        input.parentNode.classList.add('has-error');
        return false;
    }

    input.parentNode.classList.remove('has-error');
    return true;
}

/**
 * Reports if the indicated input has a value entered.
 * If it does not have a value, mark the parent div as wrong.
 * (PRIVATE)
 *
 * @param {String} id
 * @returns {Boolean}
 */
function checkInput(id) {
    const input = document.querySelector('#' + id);
    if (input.value.trim()) {
        input.parentNode.classList.remove('has-error');
        return true;
    }
    input.parentNode.classList.add('has-error');
    return false;
}

/**
 * Checks that all the required fields have a value.
 * (PRIVATE)
 *
 * @returns {Boolean}
 */
function checkForm() {
    let correctForm = checkCheckBox('rgpd');
    correctForm = checkInput('name') && correctForm;
    correctForm = checkInput('email') && correctForm;
    correctForm = checkInput('phone') && correctForm;
    return correctForm;
}

/**
 * Returns a JSON with all the data entered in the form.
 * (PRIVATE)
 *
 * @returns {Object}
 */
function getFormData() {
    return {
        'action': document.querySelector('#action').value,
        'name': document.querySelector('#name').value,
        'email': document.querySelector('#email').value ,
        'phone': document.querySelector('#phone').value
    };
}

/**
 * Receives and processes the response from the server by a request post.
 * - If the response is 201, it shows a confirmation message and clears the form.
 * - else, it shows an error message.
 *
 * @param {Event} event
 */
function processResponsePost(event) {
    if (event.target.readyState !== 4) {
        return;
    }

    if (event.target.status !== 201) {
        const error = JSON.parse(event.target.responseText);
        const message = error.message ? error.message : error.error;
        alert(message);
        return;
    }

    alert(JSON.parse(event.target.responseText).message);
    document.querySelector('#formContact').reset();
}

/**
 * Manage form confirmation.
 * - Check that all values are reported.
 * - If so, it sends the form data to the API for registration.
 * - As it is an asynchronous process, registration management is delegated
 *   to the processResponsePost process.
 *
 * @param {Event} event
 */
function formSubmit(event) {
    event.preventDefault();

    if (checkForm()) {
        const data = getFormData();
        const request = new XMLHttpRequest();
        request.addEventListener('readystatechange', processResponsePost);
        request.open("POST", 'Contact');
        request.setRequestHeader("Content-Type", "application/json");
        request.send(JSON.stringify(data));
    }
}

/*
 * M A I N
 *
 * - Capture form confirmation.
 */
window.onload = function () {
    document.querySelector('#formContact').addEventListener('submit', formSubmit);
};