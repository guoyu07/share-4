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
        <link rel="stylesheet" href="css/share.css" />
    </head>
    <body>
        <div class="row">
            <div class="large-12 columns">
                {$message}
                <form method="post" action="item/upload" enctype="multipart/form-data">
                    <div class="panel">
                        <label>File to share
                            <input type="file" placeholder="Upload a file..." id="item" name="item" />
                        </label>
                    </div>

                    <div class="panel">
                        <label>Total number of downloads (integer), or enter asterisk (*) for unlimited
                            <input type="text" placeholder="Enter an integer..." id="total" name="total" value="1" />
                        </label>

                        <label>Self destruct after downloads depleted
                            <input type="checkbox" id="destruct" name="destruct" value="destruct" />
                        </label>
                    </div>

                    <div class="panel">
                        <label>URL identifier, e.g. <code>{$cfg['base-item-url']}/{$hash}</code>
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

                <p class="light-text">Powered by <a target="_blank" href="https://github.com/wsams/share.git">share</a></p>
            </div>
        </div>
        
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script type="text/javascript">
            var baseUrl = "{$cfg['base-item-url']}";
        </script>
        <script src="js/share.js"></script>
    </body>
</html>
eof;

print($html);
