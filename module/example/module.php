<?php 
return array(
    'module' => array(
        'index' => array(
            'file' => 'Controller/index',
            'class'=> 'index',
        ),
    ),
    'callmodule' => function($module, $dir, $mod, $act){
        View::set_dir(dirname(__FILE__).'/View');
    }
);