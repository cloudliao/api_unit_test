<?php
/**
 * 安全性檢查設定
 * @author Leo.Kuo
 */
$config['secure'] = array
(
    'method_args' => array
    (
        // si_seq
        array
        (
            'type' => 'seq'
        ),
        // args
        array
        (
            'type'            => 'function',
            'filter_function' => function($args)
            {
                return $args;
            }
        ),
    ),
    'get_args' => array
    (
        array
        (
            'name' => 'sort',
            'type' => 'string'
        ),
        array
        (
            'name' => 'asc',
            'type' => 'number'
        ),
        array
        (
            'name' => 'page',
            'type' => 'number'
        ),
        array
        (
            'name' => 'debug',
            'type' => 'string'
        ),
        array
        (
            'name' => 'debug_option'
        )        
    )
);
