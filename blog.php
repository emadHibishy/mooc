<?php
session_start();
require_once 'globals.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'courses.model.php';
require_once CONTROLLERS . DS . 'front.controller.php';
$frontController = new frontController();
