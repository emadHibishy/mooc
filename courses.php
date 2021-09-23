<?php
session_start();
require_once 'globals.php';
require_once CONTROLLERS . DS . 'front.controller.php';
require_once MODELS . DS . 'courses.model.php';
require_once MODELS . DS . 'coursescategories.model.php';
$frontController = new frontController();
