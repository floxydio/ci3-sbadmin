<?php



class Auth extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form_validation->set_rules('email','Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password','Password', 'trim|required');

        if($this->form_validation->run() == false) {
        $data['title'] = "Login";
        $this->load->view('templates/auth_header', $data);
        $this->load->view('auth/login');
        $this->load->view('templates/auth_footer');
        } else {
            //Sukses
            $this->_login();
        };
    }


    private function _login() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user = $this->db->get_where('user', ['email' => $email])->row_array();



        if($user) {
            // usernya aktif
            if($user['is_active'] == 1) {
                // Cek password
                if(password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_ud' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    redirect('user');
            } else {
             $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
              This email has been not activated!
                </div>');
            redirect('auth');                
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Email is not registered!
                </div>');
            redirect('auth');            
        }
    }
}



    public function registration()
    {

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'This is email has already registered',
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'password dont match',
            'min_length' => 'password too short'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|min_length[3]|matches[password1]');



        if ($this->form_validation->run() == false) {
            $data['title'] = "Register";
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 1,
                'date_created' => time()
            ];

            $this->db->insert('user', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                Selamat! Data Kamu Telah dibuat. Silahkan Login
                </div>');
            redirect('auth');
        }
    }

    public function logout() {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');
         $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                You Have Been Logout!
                </div>');
        redirect('auth');
    }
}
