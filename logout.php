<?php
require 'globals.php';
session_start();
session_unset();
session_destroy();
Redirect::redirect('login.php');