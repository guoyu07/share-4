<?php

$cfg['items-dir'] = "../items";
$cfg['base-item-url'] = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] 
    . preg_replace("/^(.*)\/.*$/", "\${1}", $_SERVER['REQUEST_URI']) . "/item";

