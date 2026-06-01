<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cronsmodel extends CI_Model {

	

	public function index()
	{
		$where = [];
        $limit = 10;
        $page = 1;
        $order_by = [
            '_id' => -1
        ];
		$q = $this->mongo_db->where($where)->order_by($order_by)->limit($limit)->offset(($page - 1) * $limit)->get('active_crons');
        $ret = [];
        foreach($q as $row){
            $ret[] = $row;
        }
       	$this->load->view('dashboard/crons');
	}
}
