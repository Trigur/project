<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Project extends MY_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('project');
    }

    /*
        Установка модуля
    */
    public function _install() {
        if (! $this->dx_auth->is_admin()) {
            $this->core->error_404();
        }

        $this->db->where('name', 'project')->update('components', [
            'autoload' => '1',
            'enabled'  => '1',
            'in_menu'  => '0',
        ]);
    }
}