<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	protected $INPUT;

	public function __construct()
	{
		parent::__construct();
		$this->INPUT = $this->input->post();
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data['expedisi'] = $this->db->get('expedisi')->result_array();
		$this->load->view('welcome_message', $data);
	}

	public function post()
	{
		$resi = preg_split("/\r\n|\n|\r/", trim($this->input->post('resi')));
		$resi = array_map('trim', $resi);
		$this->INPUT['resi'] = $resi;
		$data['data'] = $this->INPUT;
		$data['append'] = $this->load->view('append/resi', $this->INPUT);
		// $data['append'] = $this->_append($this->INPUT);
		echo json_encode($data);
	}

	public function cekresi()
	{
		echo $this->_rajaOngkir($this->INPUT['resi'], $this->INPUT['expedisi']);
	}

	private function _append($data)
	{
		$html = "<p>Expedisi = " . $data['expedisi'] . "</p>";
		foreach ($data['resi'] as $r) {
			$html .= "<p class='$r'>Resi = " . $r . "</p>";
		}
		return $html;
	}

	private function _rajaOngkir($resi, $kurir)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "waybill=$resi&courier=$kurir",
			CURLOPT_HTTPHEADER => array(
				"content-type: application/x-www-form-urlencoded",
				"key: faebd4eaa05797439b7a2cb665393d7b"
			),
		));

		$response = json_decode(curl_exec($curl), true);


		return json_encode($response);
	}
}
