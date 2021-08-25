<?php
session_start();
require_once '..'. DIRECTORY_SEPARATOR . 'globals.php';
require_once CONTROLLERS . DIRECTORY_SEPARATOR . 'instructor.controller.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'courses.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'users.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'usergroup.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'coursesCategories.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'coursestudents.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'coursesections.model.php';
require_once MODELS . DIRECTORY_SEPARATOR . 'courseslessons.model.php';

$admin = new instructorController();