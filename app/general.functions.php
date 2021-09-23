<?php

function getErrors()
{
    if(isset($_SESSION['errors']) && count($_SESSION['errors']) > 0){
        $htmlError = '<div class="alert alert-block alert-danger fade in">
                        <button data-dismiss="alert" class="close close-sm" type="button">
                            <i class="icon-remove"></i>
                        </button>
                        <strong>Error Occured: </strong><br/>';
        $htmlError .= '<ul class="list-unstyled">';
        foreach($_SESSION['errors'] as $err){
            $htmlError .= "<li class='list-item text-danger'>$err</li>";
        }
        $htmlError .= "</ul></div>";
        unset($_SESSION['errors']);
        return $htmlError;
    } 
    return null;
}

function getSuccess()
{
    if(isset($_SESSION['success']) && count($_SESSION['success']) > 0){
        $htmlError = '<div class="alert alert-block alert-success fade in">
                        <button data-dismiss="alert" class="close close-sm" type="button">
                            <i class="icon-remove"></i>
                        </button>';
        $htmlError .= '<ul class="list-unstyled">';
        foreach($_SESSION['success'] as $err){
            $htmlError .= "<li class='list-item text-success'>$err</li>";
        }
        $htmlError .= "</ul></div>";
        unset($_SESSION['success']);
        return $htmlError;
    } 
    return null;
}

function hashPassword($password)
{
    $key = '$Mooc$Ha4$K3y$';
    return sha1(md5($password . $key));
}

function isLogin()
{
    return isset($_SESSION['user']) ? true : false;
}

function invalidRedirect($depth = '')
{
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'))[1];
    if(isLogin()){
        if($_SESSION['user']['group_id'] == 1){
            if($uri !== 'admin'){
                Redirect::redirect($depth.'admin/index.php');
                exit;
            }
        }
        elseif($_SESSION['user']['group_id'] == 2){
            if($uri !== 'instructor'){
                Redirect::redirect($depth.'instructor/index.php');
                exit;
            }
        }
        elseif($_SESSION['user']['group_id'] == 3){
            if($uri !== 'front'){
                Redirect::redirect($depth.'front/index.php');
                exit;
            }
        }else{
            Redirect::redirect($depth.'index.php');
            exit;
        }
    }else{
        Redirect::redirect($depth.'login.php');
        exit;
    }
}