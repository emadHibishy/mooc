<?php
session_start();
require_once 'globals.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'usergroup.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'users.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'courses.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'coursescategories.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'coursesections.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'courseslessons.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'lessoncomments.model.php';
require_once CONTROLLERS . DIRECTORY_SEPARATOR . 'admin.controller.php';
if(!isset($_SESSION['user'])){
    Redirect::redirect('login.php');
}