<?php
session_start();

require_once 'globals.php';
require_once CONTROLLERS . DIRECTORY_SEPARATOR . 'auth.controller.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'users.model.php';
if(isLogin()){
    invalidRedirect();
}
$auth = new Auth();

$auth->login();