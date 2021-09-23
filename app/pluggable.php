<?php
/* 
=====================
=       ACTIONS     =
=====================
*/
$actions = array();

function add_action($hook, $funcName)
{
    global $actions;
    $actions[$hook][] = $funcName;
}

function do_action($hook)
{
    global $actions;

    if(isset($actions[$hook]))
    {
        foreach($actions[$hook] as $funcName)
        {
            if(function_exists($funcName))
                call_user_func($funcName);
        }
    }
}

/* 
=====================
=       FILTERS     =
=====================
*/
$filters = array();

function add_filter($hook, $funcName)
{
    global $filters;
    $filters[$hook][] = $funcName;
}

function do_filter($hook, $content)
{
    global $filters;
    if (isset($filters[$hook]))
    {
        foreach($filters[$hook] as $funcName)
        {
            if ( function_exists($funcName) )
                $content = call_user_func($funcName, $content);
        }
    }
    return $content;
}