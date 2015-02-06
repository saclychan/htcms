<?php 
class index extends Controller
{
    function init()
    {
        // required for initial controller
    }

    function _action()
    {
        // default action
        $foo = 'this is action default<br>';
        //assign to view with name variable FOO
        $this->add('FOO', $foo);
    }
}