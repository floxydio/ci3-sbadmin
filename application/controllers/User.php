<?php 


class User extends CI_Controller {

	public function index() {
		$data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
		$this->load->view('user/index');
	}
}
