<?php

require_once("../vendor/autoload.php");

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
    $config = "../items/{$hash}/config.json";
    if (file_exists($config)) {
        $json = json_decode(file_get_contents($config), true);
        $item = "../items/{$hash}/{$json['item']}";

        if ($json['type'] === "count" && $json['count'] < $json['total']) {
            $update = $json;
            $update['count'] = $json['count'] + 1;
            file_put_contents($config, json_encode($update, JSON_PRETTY_PRINT));

            $app->response()->header("Content-Type", $json['mime']);
            $app->response->setStatus(200);
            readfile($item);
        } else {
            $app->response()->header("Content-Type", "text/html");
            $app->response->setStatus(404);
            print("<html><body>Item expired</body></html>");
        }
    } else {
        $app->response()->header("Content-Type", "text/html");
        $app->response->setStatus(404);
        print("<html><body>Item not found</body></html>");
    }
});

$app->run();
