function validate() {
    var total = $("#total").val();
    if (undefined !== total && total != null) {
        total = total.replace(/\s*/, "");
    }
    var totalInt = parseInt(total, 10);
    if (!isNaN(totalInt)) {
        return true;
    } else if (total === "*") {
        return true;
    } else {
        return false;
    }
}

function validateName(baseUrl) {
    var name = $("#name").val();
    $.getJSON("item/validate/name/" + encodeURIComponent(name), function(json) {
        if (json.status === "available") {
            $("#name-message").html("Name is available").removeClass("error").addClass("success");
            $("#share-btn").attr("disabled", false);
            var itemUrl = baseUrl + "/" + encodeURIComponent(name);
            $("#item-url").html(itemUrl).attr("href", itemUrl);
            $("#itemUrl").val(itemUrl);
        } else if (json.status === "taken") {
            $("#name-message").html("Name is taken").addClass("error").removeClass("success");
            $("#share-btn").attr("disabled", "disabled");
        }
    });
}

$(document).foundation();

$(document).ready(function () {
    $("#share-btn").attr("disabled", "disabled");

    validateName(baseUrl);

    $("#name").on("keyup", function () {
        validateName(baseUrl);
    });

    $("#item").on("change", function () {
        $("#mime").val("");
        $.getJSON("item/validate/mime/" + encodeURIComponent($("#item").val()), function(json) {
            if (json.status === "exists") {
                $("#mime").val(json.mime);
            }
        });
    });
});
