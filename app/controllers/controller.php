<?php

class Controller
{
    protected function setError($errors)
    {
        if(is_array($errors)){
            foreach($errors as $error){
                $_SESSION['errors'][] = $error;
            }
        }else{
        $_SESSION['errors'][] = $errors;
        }
    }

    protected function setSuccess($success)
    {
        if(is_array($success)){
            foreach($success as $success){
                $_SESSION['success'][] = $success;
            }
        }else{
        $_SESSION['success'][] = $success;
        }
    }

    public function checkPermission()
    {
        invalidRedirect('../');
    }

    public function render($view, $data = array())
    {
        if(!empty($data))
            extract($data);
        $groupName = str_replace('controller', '', strtolower(get_called_class()));
    
        if($view !== 'index'){
            $pageName = strtoupper($view);
        }
        include TEMPLATES . DS . $groupName. DS . 'header.html';
        if(file_exists(TEMPLATES . DS . $groupName. DS . 'breadcrumbs.html') && $view !== 'index') {
            include_once TEMPLATES . DS . $groupName. DS . 'breadcrumbs.html';
        }
        if ( file_exists(TEMPLATES . DS . $groupName. DS . 'menu.html') ) {
            include_once TEMPLATES . DS . $groupName. DS . 'menu.html';
        }
        require VIEWS . DS. $groupName . DS . $view.'.html';
        include TEMPLATES . DS . $groupName. DS . 'footer.html';
    }
    
}