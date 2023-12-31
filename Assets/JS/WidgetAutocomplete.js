/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
function widgetAutocompleteGetData(formId, formData, term) {
    let rawForm = $("form[id=" + formId + "]").serializeArray();
    $.each(rawForm, function (i, input) {
        formData[input.name] = input.value;
    });
    formData["action"] = "autocomplete";
    formData["term"] = term;
    return formData;
}

$(document).ready(function () {
    $(".widget-autocomplete").each(function () {
        let data = {
            field: $(this).attr("data-field"),
            fieldcode: $(this).attr("data-fieldcode"),
            fieldfilter: $(this).attr("data-fieldfilter"),
            fieldtitle: $(this).attr("data-fieldtitle"),
            source: $(this).attr("data-source"),
            strict: $(this).attr("data-strict")
        };
        let formId = $(this).closest("form").attr("id");
        $(this).autocomplete({
            source: function (request, response) {
                $.ajax({
                    method: "POST",
                    url: window.location.href,
                    data: widgetAutocompleteGetData(formId, data, request.term),
                    dataType: "json",
                    success: function (results) {
                        let values = [];
                        results.forEach(function (element) {
                            if (element.key === null || element.key === element.value) {
                                values.push(element);
                            } else {
                                values.push({key: element.key, value: element.key + " | " + element.value});
                            }
                        });
                        response(values);
                    },
                    error: function (msg) {
                        alert(msg.status + " " + msg.responseText);
                    }
                });
            },
            select: function (event, ui) {
                if (ui.item.key !== null) {
                    $("form[id=" + formId + "] input[name=" + data.field + "]").val(ui.item.key);
                    let value = ui.item.value.split(" | ");
                    ui.item.value = value.length > 1 ? value[1] : value[0];
                }
            },
            open: function (event, ui) {
                $(this).autocomplete('widget').css('z-index', 1500);
                return false;
            }
        });
    });
});