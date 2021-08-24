<?php

class studentController extends Controller
{
    public function __construct()
    {
        $this->checkPermission();
        $this->render();
    }
}