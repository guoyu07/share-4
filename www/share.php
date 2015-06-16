<?php

require_once("../config.php");
require_once("../lib/mimes.php");

$hash = substr(sha1(microtime()), 0, 8);
$config = "../items/{$hash}/config.json";
while (file_exists($config)) {
    $hash = substr(sha1(microtime()), 0, 8);
    $config = "../items/{$hash}/config.json";
}

$message = "";
if ($_GET['a'] === "s") {
    $message = "<div class=\"alert-box radius secondary\">";
    $message .= "<p>File uploaded and ready for sharing.</p>";
    $message .= "<p><a href=\"{$_GET['u']}\">{$_GET['u']}</a></p>";
    $message .= "</div>";
}

$html .= <<<eof
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Share</title>
        <link rel="stylesheet" href="css/foundation.css" />
        <style type="text/css">
            body {
                margin:16px;
            }
            .success {
                display: block;
                padding: 0.375rem 0.5625rem 0.5625rem;
                margin-top: -1px;
                margin-bottom: 1rem;
                font-size: 0.75rem;
                font-weight: normal;
                font-style: italic;
                background: #24c127;
                color: #ffffff;
            }
            .alert-box p {
                margin-bottom:0em;
            }
        </style>
    </head>
    <body>
        <div class="row">
            <div class="large-12 columns">
                {$message}
                <form method="post" action="item/upload" enctype="multipart/form-data">
                    <div class="panel">
                        <label>File to share.
                            <input type="file" placeholder="Upload a file..." id="item" name="item" />
                        </label>
                    </div>

                    <div class="panel">
                        <label>Total number of downloads (integer), or enter asterisk (*) for unlimited.
                            <input type="text" placeholder="Enter an integer..." id="total" name="total" value="1" />
                        </label>
                    </div>

                    <div class="panel">
                        <label>An item name - this will be used in the URL. For example, if the name
                                is <code>abcd</code> then the URL would be <code>{$cfg['base-item-url']}/abcd</code>. This
                                may be an obfuscated hash or the exact file name depending on usage.
                            <input type="text" placeholder="Enter an item name..." id="name" name="name" value="{$hash}" />
                            <small id="name-message"></small>
                        </label>
                    </div>

                    <div class="panel">
                        <label>A mime type will be guessed, otherwise you may find it here:
                                <a target="_blank" href="https://en.wikipedia.org/wiki/Internet_media_type">wiki:Internet media type</a>
                            <input type="text" placeholder="Enter a mime type..." id="mime" name="mime" />
                        </label>
                    </div>

                    <div class="panel">
                        <label>Item URL, take note before clicking Share: <a id="item-url" href="#"></a></label>
                    </div>

                    <input type="hidden" name="itemUrl" id="itemUrl" value="" />
                    <input id="share-btn" class="button" type="submit" value="Share" onclick="return validate()" />

                </form>
            </div>
        </div>
        
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script>
            var baseUrl = "{$cfg['base-item-url']}";

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

            function validateName() {
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

                validateName();

                $("#name").on("keyup", function () {
                    validateName();
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
        </script>
    </body>
</html>
eof;

print($html);
