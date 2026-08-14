<?php
// public/index.php
session_start();

require_once '../core/Router.php';

$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard/index';

$router = new Router();
$router->run($url);
