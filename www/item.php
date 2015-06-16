<?php

require_once("../config.php");
require_once("../vendor/autoload.php");
require_once("../lib/mimes.php");

date_default_timezone_set("America/Indianapolis");

session_start();
$sessid = session_id();

function response($arr) {
    print(json_encode($arr));
}

function responseByStatus($arr, $status, $app) {
    $app->response->setStatus($status);
    response($arr);
}

$app = new \Slim\Slim();

$app->get('/item/:hash', function ($hash) use ($app) {
    $send = false;
    $config = "../items/{$hash}/config.json";
    if (file_exists($config)) {
        $json = json_decode(file_get_contents($config), true);
        $item = "../items/{$hash}/{$json['item']}";

        if ($json['type'] === "count" && ($json['count'] < $json['total'] || $json['total'] === "*")) {
            $update = $json;
            $update['count'] = $json['count'] + 1;
            file_put_contents($config, json_encode($update, JSON_PRETTY_PRINT));
            $send = true;
        } else {
            $app->response()->header("Content-Type", "text/html");
            $app->response->setStatus(404);
            die("<html><body>Item expired</body></html>");
        }

        if ($send) {
            $mime = isset($json['mime']) ? $json['mime'] : "application/x-download";

            $app->response()->header("Content-Description", $json['item']); 
            $app->response()->header("Content-Type", $mime);
            $app->response()->header("Content-Length", filesize($item)); 
            // Will cause the file to download. In the case of an image the browser will not render the image.
            //$app->response()->header("Content-Disposition", "attachment; filename = " . basename($json['item'])); 
            //$app->response()->header("Content-Transfer-Encoding: binary"); 
            $app->response()->header("Cache-Control", "must-revalidate, post-check = 0, pre-check = 0"); 
            $app->response()->header("Expires", "0"); 
            $app->response()->header("Pragma", "public"); 
            $app->response->setStatus(200);

            readfile($item);
        } else {
            $app->response()->header("Content-Type", "text/html");
            $app->response->setStatus(403);
            die("<html><body>Item access error</body></html>");
        }
    } else {
        $app->response()->header("Content-Type", "text/html");
        $app->response->setStatus(404);
        die("<html><body>Item not found</body></html>");
    }
});

$app->get('/item/validate/name/:name', function ($name) use ($app, $cfg) {
    $app->response()->header("Content-Type", "application/json");
    $app->response->setStatus(200);
    if (file_exists("{$cfg['items-dir']}/{$name}")) {
        die(json_encode(array("status" => "taken")));
    } else {
        die(json_encode(array("status" => "available")));
    }
});

$app->get('/item/validate/mime/:filename', function ($filename) use ($app, $cfg, $mimes) {
    $app->response()->header("Content-Type", "application/json");
    $app->response->setStatus(200);
    $ext = preg_replace("/^.*\.(.*)$/", "\${1}", $filename);
    if (array_key_exists($ext, $mimes)) {
        $mime = $mimes[$ext];
        die(json_encode(array("status" => "exists", "mime" => $mime)));
    } else {
        die(json_encode(array("status" => "exists", "mime" => "application/octet-stream", 
            "message" => "mime could not be determined")));
    }
});


$app->post('/item/upload', function () use ($app, $cfg) {
    if($app->request()->isPost()) {
        $name = $_FILES['item']['name'];
        $tmp_name = $_FILES['item']['tmp_name'];
        $total = $_POST['total'];
        $hash = $_POST['name'];
        $mime = $_POST['mime'];

        $configDir = "../items/{$hash}";
        $config = "{$configDir}/config.json";
        $file = "{$configDir}/{$name}";
        $configJson = array("type" => "count", "total" => $total, "count" => 0,
                "item" => $name, "mime" => $mime);
        $json = json_encode($configJson);

        if (!file_exists($configDir)) {
            mkdir($configDir);
        }

        file_put_contents($config, $json);
        move_uploaded_file($tmp_name, $file);
    }

    $app->redirect("../share.php?a=s&u=" . urlencode($_POST['itemUrl']));
});

$app->run();
