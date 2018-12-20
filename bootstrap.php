<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function autoload($classe)
{
    if(file_exists('Models/'.$classe . '.php')) require 'Models/'.$classe . '.php';
    if(file_exists('Repositories/'.$classe . '.php')) require 'Repositories/'.$classe . '.php';
    if(file_exists('Controllers/'.$classe . '.php')) require 'Controllers/'.$classe . '.php';
}
spl_autoload_register('autoload'); // On enregistre la fonction en autoload pour qu'elle soit appelée dès qu'on instancie une classe non déclarée.
