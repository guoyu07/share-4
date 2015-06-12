<?php

require_once("../config.php");

$html .= <<<eof
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Share</title>
        <link rel="stylesheet" href="css/foundation.css" />
        <script src="js/vendor/modernizr.js"></script>
        <style type="text/css">
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
        </style>
    </head>
    <body>
        <div class="row">
            <div class="large-12 columns">
<!-- {
"type": "count",
"total": 2,
"count": 2,
"item": "InkSignedDocuments.pdf",
"mime": "application\/pdf"
} 
-->
                <label>Total number of downloads
                    <input type="text" placeholder="Enter an integer..." id="total" />
                </label>

                <label>An item name - this will be used in the URL. For example, if the name
                        is <code>abcd</code> then the URL would be <code>{$cfg['base-item-url']}/abcd</code>. This
                        may be an obfuscated hash or the exact file name depending on usage.
                    <input type="text" placeholder="Enter an item name..." id="name" />
                    <small id="name-message"></small>
                </label>

                <label>A mime type. This application does not attempt to guess the mime type of the file
                        you upload. If you don't know the mime type you will find a large list here: 
                        <a target="_blank" href="https://en.wikipedia.org/wiki/Internet_media_type">wiki:Internet media type</a>
                    <input type="text" placeholder="Enter a mime type..." id="mime" />
                </label>
            </div>
        </div>
        
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script>
            $(document).foundation();
            $(document).ready(function () {
                $("#name").on("keyup", function () {
                    var name = $("#name").val();
                    $.getJSON("item/validate/name/" + encodeURIComponent(name), function(json) {
                        if (json.status === "available") {
                            $("#name-message").html("Name is available").removeClass("error").addClass("success");
                        } else if (json.status === "taken") {
                            $("#name-message").html("Name is taken").addClass("error").removeClass("success");
                        }
                    });
                });
            });
        </script>
    </body>
</html>
eof;

print($html);
