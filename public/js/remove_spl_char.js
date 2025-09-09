function stripTags(value) {
    return $('<div>').html(value).text();
}

function sanitizeInput(el) {
    el.value = stripTags(el.value);
}
