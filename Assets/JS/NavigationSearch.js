$(document).ready(function () {
    function autocompleteGetData(formId, formData) {
        let rawForm = $("form[id=" + formId + "]").serializeArray();
        $.each(rawForm, function (i, input) {
            formData[input.name] = input.value;
        });
        formData["action"] = "autocomplete";
        return formData;
    }

    $("#navQuery").each(function () {
        let data = {}
        let formId = $(this).closest("form").attr("id");
        $(this).autocomplete({
            source: function (request, response) {
                $.ajax({
                    method: "POST",
                    url: window.location.href,
                    data: autocompleteGetData(formId, data),
                    dataType: "json",
                    success: function (results) {
                        let values = [];
                        results.forEach(function (element) {
                            if (element.key === null || element.key === element.value) {
                                values.push(element);
                            } else {
                                values.push({key: element.key, value: element.value});
                            }
                        });
                        response(values);
                    },
                    error: function (msg) {
                        console.log('status:', msg.status)
                        console.log('response:', msg.responseText);
                    }
                });
            },
            select: function (event, ui) {
                if (ui.item.key !== null) {
                    window.location.replace('BookDetail?code=' + ui.item.key);
                }
            },
            open: function (event, ui) {
                $(this).autocomplete('widget').css('z-index', 999999);
                return false;
            }
        });
    });
});