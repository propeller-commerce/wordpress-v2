<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$ref = 'Propeller\Custom\Includes\Controller\MachineAjaxController';

$AjaxMachine = class_exists($ref, true) 
                    ? new $ref()
                    : new Propeller\Includes\Controller\MachineAjaxController();

add_filter('query_vars', 'machine_query_vars');

add_action('wp_ajax_do_machine', array($AjaxMachine, 'do_machine'));
add_action('wp_ajax_nopriv_do_machine', array($AjaxMachine, 'do_machine'));

function machine_query_vars($qvars) {
    $qvars[] = 'action';
    $qvars[] = 'page';
    $qvars[] = 'offset';
    
    return $qvars;
}