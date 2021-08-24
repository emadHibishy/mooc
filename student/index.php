<?php
session_start();
require_once '..'. DIRECTORY_SEPARATOR . 'globals.php';
require_once CONTROLLERS . DIRECTORY_SEPARATOR . 'student.controller.php';

$student = new studentController();
