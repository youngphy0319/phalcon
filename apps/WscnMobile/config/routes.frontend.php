<?php

return array(
    '/' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'index',
    ),
    '/news' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'news',
    ),
    '/news/(\d+)' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'news',
        'action' => 'news',
        'id' => 1 
    ),
    '/markets' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'markets',
    ),
    '/markets/(\w+)' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'markets',
        'action' => 'quote',
        'id' => 1 
    ),
    '/livenews' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'livenews',
    ),
    '/livenews/(\d+)' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'livenews',
        'action' => 'livenews',
        'id' => 1 
    ),
    '/calendar' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'calendar',
    ),
    '/search' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'search',
    ),
    '/search/suggestion' =>  array(
        'module' => 'WscnMobile',
        'controller' => 'search',
        'action' => 'suggestion',
    ),
);
