<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

	<title>Hello, world!</title>
</head>

<body>
	<div class="container p-3">
		<h1>Cek Resi</h1>

		<!-- <form action="welcome/cekresi" method="post"> -->
		<div class="form-floating">
			<select name="expedisi" class="form-select" id="floatingSelect" aria-label="Floating label select example" required>
				<option value="" selected disabled>-- PILIH EXPEDISI --</option>
				<?php foreach ($expedisi as $value) : ?>
					<option value="<?= $value['kode']; ?>"><?= $value['nama']; ?></option>
				<?php endforeach; ?>
			</select>
			<label for="floatingSelect">EXPEDISI</label>
		</div>
		<div class="form-floating mt-3">
			<textarea name="resi" class="form-control" style="height: 250px;" id="resi"></textarea>
			<label for="resi">NO RESI</label>
		</div>
		<button class="cek btn btn-danger my-3" type="submit">Proses</button>
		<!-- </form> -->
		<!-- <div class="result mt-3"></div> -->
		<table id="myTable" class="mt-3" style="width: 100%">
			<thead>
				<tr>
					<th>Resi</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody class="result"></tbody>
		</table>
		<tr>
			<td></td>
		</tr>
	</div>

	<!-- Optional JavaScript; choose one of the two! -->

	<!-- Option 1: Bootstrap Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

	<!-- Option 2: Separate Popper and Bootstrap JS -->

	<!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script> -->
	<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script>
		$('.cek').on('click', () => {
			$('.result').empty().append();
			var e = document.getElementById("floatingSelect");
			var expedisi = e.value;
			var r = document.getElementById("resi");
			var resi = r.value;
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '<?= base_url('welcome/post'); ?>',
				data: {
					expedisi: expedisi,
					resi: resi
				},
				success: proses
			});
		});

		function proses(data) {
			$('.result').append(data.append);
			data.data.resi.forEach(element => {
				$.ajax({
					type: 'post',
					url: '<?= base_url('welcome/cekresi'); ?>',
					data: {
						expedisi: data.data.expedisi,
						resi: element
					},
					success: (data) => {
						var data = JSON.parse(data);
						console.log(data['rajaongkir']);
						if (data['rajaongkir']['status']['code'] == 200) { // Response
							var result = data['rajaongkir']['result'];
							var kurir = data['rajaongkir']['query']['courier']


							if (!result['delivered']) { // Status sampai
								var manifestLength = result['manifest'].length - 1;
								var manifest;

								if (kurir == 'sicepat') {
									manifest = result['manifest'][manifestLength]['manifest_code'];
								} else {
									manifest = result['manifest'][0]['manifest_code'];
								}
								console.log(manifest);;

								switch (manifest) {
									case '9':
										$('.' + element).append(' <span class="bg-danger">RETUR</span>');
										break;
									case 'RETURN TO SHIPPER':
										$('.' + element).append(' <span class="bg-danger">RETUR</span>');
										break;
									default:
										$('.' + element).append(' <span class="bg-info">' + result['delivery_status']['status'] + '</span>');
										break;
								}
							} else {
								$('.' + element).append(' <span class="bg-success">' + result['delivery_status']['status'] + '</span>');
							}
						} else {
							$('.' + element).append(' ERROR');
						}
					}
				});
			});
		}
	</script>


</body>

</html>