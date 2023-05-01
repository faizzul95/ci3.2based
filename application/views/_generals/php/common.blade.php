<?php

if (isset($_POST['fileName'])) {
	$filename = $_POST['fileName'];
	$data = $_POST['dataArray'];
	$filePath = "../../../application/views/$filename";

	// dd($filePath);
	if (file_exists($filePath)) {
		$opts = array(
			'http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => (!empty($data)) ? http_build_query($data) : null
			)
		);

		$context  = stream_context_create($opts);
		echo file_get_contents($filePath, false, $context);
	} else {
		// echo "File does not exist.";
		echo '<div class="alert alert-danger" role="alert">
                File <b><i>' . $filePath . '</i></b> does not exist.
               </div>';
	}
}

?>

<script>
	function loadFileContent(fileName, idToLoad, sizeModal = 'lg', title = 'Default Title', dataArray = null, typeModal = 'modal') {

		if (typeModal == 'modal') {
			var idContent = idToLoad + "-" + sizeModal;
		} else {
			var idContent = "offCanvasContent-right";
		}

		$('#' + idContent).empty(); // reset

		return $.ajax({
			type: "POST",
			url: $('meta[name="base_url"]').attr('content') + 'public/custom/php/general.php',
			data: {
				baseUrl: $('meta[name="base_url"]').attr('content'),
				fileName: fileName,
				dataArray: dataArray,
				// _token: Cookies.get(csrf_cookie_name)
				'cid': Cookies.get(csrf_cookie_name) // csrf token
			},
			headers: {
				"Authorization": "Bearer " + Cookies.get(csrf_cookie_name),
				"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
			},
			dataType: "html",
			success: function(data) {
				$('#' + idContent).append(data);

				setTimeout(function() {
					if (typeof getPassData == 'function') {
						getPassData($('meta[name="base_url"]').attr('content'), Cookies.get(csrf_cookie_name), dataArray);
					} else {
						console.log('function getPassData not initialize!');
					}
				}, 50);

				if (typeModal == 'modal') {
					$('#generalTitle-' + sizeModal).text(title);
					$('#generalModal-' + sizeModal).modal('show');
				} else {
					// reset
					$('.custom-width').css('width', '400px');

					$('#offCanvasTitle-right').text(title);
					$('#generaloffcanvas-right').offcanvas('toggle');
					$('.custom-width').css('width', sizeModal);
				}
			}
		});
	}

	function loadFormContent(fileName, idToLoad, sizeModal = 'lg', urlFunc = null, title = 'Default Title', dataArray = null, typeModal = 'modal') {

		if (typeModal == 'modal') {
			var idContent = idToLoad + "-" + sizeModal;
		} else {
			var idContent = "offCanvasContent-right";
		}

		$('#' + idContent).empty(); // reset

		return $.ajax({
			type: "POST",
			url: $('meta[name="base_url"]').attr('content') + 'public/custom/php/general.php',
			data: {
				baseUrl: $('meta[name="base_url"]').attr('content'),
				fileName: fileName,
				dataArray: dataArray,
				// _token: Cookies.get(csrf_cookie_name)
				'cid': Cookies.get(csrf_cookie_name) // csrf token
			},
			headers: {
				"Authorization": "Bearer " + Cookies.get(csrf_cookie_name),
				"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
			},
			dataType: "html",
			success: function(response) {
				$('#' + idContent).append(response);

				setTimeout(function() {
					if (typeof getPassData == 'function') {
						getPassData($('meta[name="base_url"]').attr('content'), Cookies.get(csrf_cookie_name), dataArray);
					} else {
						console.log('function getPassData not initialize!');
					}
				}, 50);

				// get form id
				var formID = $('#' + idContent + ' > form').attr('id');
				// > div:first-child

				$("#" + formID)[0].reset(); // reset form
				document.getElementById(formID).reset(); // reset form
				$("#" + formID).attr('action', urlFunc); // set url

				if (typeModal == 'modal') {
					$('#generalTitle-' + sizeModal).text(title);
					$('#generalModal-' + sizeModal).modal('show');
					$("#" + formID).attr("data-modal", '#generalModal-' + sizeModal);
				} else {
					// reset
					$('.custom-width').css('width', '400px');

					$('#offCanvasTitle-right').text(title);
					$('#generaloffcanvas-right').offcanvas('toggle');
					$("#" + formID).attr("data-modal", '#generaloffcanvas-right');
					$('.custom-width').css('width', sizeModal);
				}

				if (dataArray != null) {
					$.each($('input, select ,textarea', "#" + formID), function(k) {
						var type = $(this).prop('type');
						var name = $(this).attr('name');

						if (type == 'radio' || type == 'checkbox') {
							$("input[name=" + name + "][value='" + dataArray[name] + "']").prop("checked", true);
						} else {
							$('#' + name).val(dataArray[name]);
						}

					});
				}

			}
		});
	}

	function generateDatatable(id, typeTable = 'client', url = null, nodatadiv = 'nodatadiv', dataObj = null, filterColumn = []) {

		const tableID = $('#' + id);
		var table = tableID.DataTable().clear().destroy();

		$.ajaxSetup({
			data: {
				'cid': Cookies.get(csrf_cookie_name) // csrf token
			}
		});

		if (typeTable == 'client') {

			return tableID.DataTable({
				// "pagingType": "full_numbers",
				'paging': true,
				'ordering': true,
				'info': true,
				'lengthChange': true,
				'responsive': false,
				'language': {
					"searchPlaceholder": 'Search...',
					"sSearch": '',
					// "lengthMenu": '_MENU_ item / page',
					// "paginate": {
					// 	"first": "First",
					// 	"last": "The End",
					// 	"previous": "Previous",
					// 	"next": "Next"
					// },
					// "info": "Showing _START_ to _END_ of _TOTAL_ items",
					// "emptyTable": "No data is available in the table",
					// "info": "Showing _START_ to  _END_ of  _TOTAL_ items",
					// "infoEmpty": "Showing 0 to 0 of 0 items",
					// "infoFiltered": "(filtered from _MAX_ number of items)",
					// "zeroRecords": "No matching records",
					// "processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment..",
					// "loadingRecords": "Loading...",
					// "infoPostFix": "",
					// "thousands": ",",
				},
			});

		} else {

			if (dataObj != null) {
				dataObj['cid'] = Cookies.get(csrf_cookie_name) // csrf token
				dataSent = dataObj;
			} else {
				dataSent = null;
			}

			let columnAction = [{
				"render": function(data, type, row) {
					return data;
				},
				"width": "14%",
				"targets": -1,
				"searchable": false,
				"orderable": false
			}];

			if (dataSent == null) {
				return tableID.DataTable({
					// "pagingType": "full_numbers",
					"processing": true,
					"serverSide": true,
					"responsive": true,
					"iDisplayLength": 10,
					"bLengthChange": true,
					"searching": true,
					"autoWidth": false,
					"ajax": {
						type: 'POST',
						url: $('meta[name="base_url"]').attr('content') + url,
						dataType: "JSON",
						// data: dataSent,
						headers: {
							"Authorization": "Bearer " + Cookies.get(csrf_cookie_name),
							'X-Requested-With': 'XMLHttpRequest',
							'content-type': 'application/x-www-form-urlencoded',
							"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
						},
						"error": function(xhr, error, exception) {
							if (exception) {
								if (isError(xhr.status))
									noti(xhr.status, exception);
							}
						}
					},
					"language": {
						"searchPlaceholder": 'Search...',
						"sSearch": '',
						// "lengthMenu": '_MENU_ item / page',
						// "paginate": {
						// 	"first": "First",
						// 	"last": "The End",
						// 	"previous": "Previous",
						// 	"next": "Next"
						// },
						// "info": "Showing _START_ to _END_ of _TOTAL_ items",
						// "emptyTable": "No data is available in the table",
						// "info": "Showing _START_ to _END_ of _TOTAL_ items",
						// "infoEmpty": "Showing 0 to 0 of 0 items",
						// "infoFiltered": "(filtered from _MAX_ number of items)",
						// "zeroRecords": "No matching records",
						// "processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment.. ",
						// "loadingRecords": "Loading...",
						// "infoPostFix": "",
						// "thousands": ",",
					},
					"columnDefs": [...filterColumn, ...columnAction],
					initComplete: function() {

						var totalData = this.api().data().length;

						if (totalData > 0) {
							$('#' + nodatadiv).hide();
							$('#' + id + 'Div').show();
						} else {
							tableID.DataTable().clear().destroy();
							$('#' + id + 'Div').hide();
							$('#' + nodatadiv).show();
						}

					}
				});
			} else {
				return tableID.DataTable({
					// "pagingType": "full_numbers",
					"processing": true,
					"serverSide": true,
					"responsive": true,
					"iDisplayLength": 10,
					"bLengthChange": true,
					"searching": true,
					"ajax": {
						type: 'POST',
						url: $('meta[name="base_url"]').attr('content') + url,
						dataType: "JSON",
						data: dataSent,
						headers: {
							"Authorization": "Bearer " + Cookies.get(csrf_cookie_name),
							'X-Requested-With': 'XMLHttpRequest',
							'content-type': 'application/x-www-form-urlencoded',
							"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
						},
						"error": function(xhr, error, exception) {
							if (exception) {
								if (isError(xhr.status))
									noti(xhr.status, exception);
							}
						}
					},
					"language": {
						"searchPlaceholder": 'Search...',
						"sSearch": '',
						// "lengthMenu": '_MENU_ item / page',
						// "paginate": {
						// 	"first": "First",
						// 	"last": "The End",
						// 	"previous": "Previous",
						// 	"next": "Next"
						// },
						// "info": "Showing _START_ to _END_ of _TOTAL_ items",
						// "emptyTable": "No data is available in the table",
						// "info": "Showing _START_ to _END_ of _TOTAL_ items",
						// "infoEmpty": "Showing 0 to 0 of 0 items",
						// "infoFiltered": "(filtered from _MAX_ number of items)",
						// "zeroRecords": "No matching records",
						// "processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment.. ",
						// "loadingRecords": "Loading...",
						// "infoPostFix": "",
						// "thousands": ",",
					},
					"columnDefs": [...filterColumn, ...columnAction],
					initComplete: function() {

						var totalData = this.api().data().length;

						if (totalData > 0) {
							$('#' + nodatadiv).hide();
							$('#' + id + 'Div').show();
						} else {
							tableID.DataTable().clear().destroy();
							$('#' + id + 'Div').hide();
							$('#' + nodatadiv).show();
						}

					}
				});
			}

		}
	}
</script>