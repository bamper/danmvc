<?php

use danperron\danmvc\Core\Application;
use danperron\danmvc\Core\Config\JSONConfiguration;

require_once '../../danperron/danmvc/Core/Application.php';

Application::registerAutoloader();

$configFile = __DIR__ . '/app/config/config.json';


$host = 'localhost';
$dbname = 'BlogSample';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$app = new Application(new JSONConfiguration($configFile));
$app->setProperty('PDO', $pdo);
$app->run();
