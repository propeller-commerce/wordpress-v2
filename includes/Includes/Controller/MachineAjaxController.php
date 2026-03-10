<?php 
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\MachineController;
use Propeller\Propeller;

class MachineAjaxController extends BaseAjaxController {
    protected $machine;

    public function __construct() { 
        parent::__construct();

        $this->machine = new MachineController();
    }

    public function do_machine() {
        $this->init_ajax();

	    $data = $this->sanitize($_POST);

        unset($data['action']);

        $response = $this->machine->machine_listing($data, true);

        die(json_encode($response));
    }
}