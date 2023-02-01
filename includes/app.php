<?php 

require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); //LAMAR DEPENDENCIA DE VLUCAS DOTENV
$dotenv->safeLoad();  //SI EL ARCHIVO NO EXISTE NO MARCARA ERROR

require 'funciones.php';
require 'database.php';

// Conectarnos a la base de datos
use Model\ActiveRecord;
ActiveRecord::setDB($db);