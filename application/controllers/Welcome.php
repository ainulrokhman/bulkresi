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

	public function index()
	{
		// $data['expedisi'] = $this->db->get('expedisi')->result_array();
		$data['expedisi'] = [
			[
				'kode' => 'jnt',
				'nama' => 'J&T'
			],
			[
				'kode' => 'sicepat',
				'nama' => 'Sicepat'
			],
		];
		$this->load->view('welcome_message', $data);
	}

	public function post()
	{
		$resi = preg_split("/\r\n|\n|\r/", trim($this->input->post('resi')));
		$resi = array_map('trim', $resi);
		$this->INPUT['resi'] = $resi;
		$data['data'] = $this->INPUT;
		// $data['append'] = $this->load->view('append/resi', $this->INPUT);
		$data['append'] = $this->_append($this->INPUT);
		echo json_encode($data);
	}

	public function cekresi()
	{
		echo $this->_rajaOngkir($this->INPUT['resi'], $this->INPUT['expedisi']);
	}

	private function _append($data)
	{
		$html = "";
		foreach ($data['resi'] as $r) {
			// $html .= "<p class='$r'>Resi = " . $r . "</p>";

			$html .= "<tr>
						<td>$r</td>
						<td class='$r'></td>
					</tr>";
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
