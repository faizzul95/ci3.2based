<?php

if (isset($_POST['fileName'])) {
	$filename = $_POST['fileName'];
	$data = isset($_POST['dataArray']) ? $_POST['dataArray'] : [];
	$filePath = "../../../application/views/$filename";

	// dd($filePath);
	if (file_exists($filePath)) {
		$opts = array(
			'http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => (!empty($data)) ? http_build_query($data) : NULL
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
	// function loadFileContent(fileName, idToLoad, sizeModal = 'lg', title = 'Default Title', dataArray = null, typeModal = 'modal') {

	// 	if (typeModal == 'modal') {
	// 		var idContent = idToLoad + "-" + sizeModal;
	// 	} else {
	// 		var idContent = "offCanvasContent-right";
	// 	}

	// 	$('#' + idContent).empty(); // reset

	// 	return $.ajax({
	// 		type: "POST",
	// 		url: $('meta[name="base_url"]').attr('content') + 'public/custom/php/general.php',
	// 		data: {
	// 			baseUrl: $('meta[name="base_url"]').attr('content'),
	// 			fileName: fileName,
	// 			dataArray: dataArray,
	// 			// _token: Cookies.get('arcaCookieSecurityCsrf')
	// 			'arcaSecurityCsrf': Cookies.get('arcaCookieSecurityCsrf') // csrf token
	// 		},
	// 		headers: {
	// 			"Authorization": "Bearer " + Cookies.get('arcaCookieSecurityCsrf'),
	// 			"X-CSRF-TOKEN": Cookies.get('arcaCookieSecurityCsrf'),
	// 		},
	// 		dataType: "html",
	// 		success: function(data) {
	// 			$('#' + idContent).append(data);

	// 			setTimeout(function() {
	// 				if (typeof getPassData == 'function') {
	// 					getPassData($('meta[name="base_url"]').attr('content'), Cookies.get('arcaCookieSecurityCsrf'), dataArray);
	// 				} else {
	// 					console.log('function getPassData not initialize!');
	// 				}
	// 			}, 50);

	// 			if (typeModal == 'modal') {
	// 				$('#generalTitle-' + sizeModal).text(title);
	// 				$('#generalModal-' + sizeModal).modal('show');
	// 			} else {
	// 				$('#offCanvasTitle-right').text(title);
	// 				$('#generaloffcanvas-right').offcanvas('toggle');
	// 			}
	// 		}
	// 	});
	// }

	// function loadFormContent(fileName, idToLoad, sizeModal = 'lg', urlFunc = null, title = 'Default Title', dataArray = null, typeModal = 'modal') {

	// 	if (typeModal == 'modal') {
	// 		var idContent = idToLoad + "-" + sizeModal;
	// 	} else {
	// 		var idContent = "offCanvasContent-right";
	// 	}

	// 	$('#' + idContent).empty(); // reset

	// 	return $.ajax({
	// 		type: "POST",
	// 		url: $('meta[name="base_url"]').attr('content') + 'public/custom/php/general.php',
	// 		data: {
	// 			baseUrl: $('meta[name="base_url"]').attr('content'),
	// 			fileName: fileName,
	// 			dataArray: dataArray,
	// 			// _token: Cookies.get('arcaCookieSecurityCsrf')
	// 			'arcaSecurityCsrf': Cookies.get('arcaCookieSecurityCsrf') // csrf token
	// 		},
	// 		headers: {
	// 			"Authorization": "Bearer " + Cookies.get('arcaCookieSecurityCsrf'),
	// 			"X-CSRF-TOKEN": Cookies.get('arcaCookieSecurityCsrf'),
	// 		},
	// 		dataType: "html",
	// 		success: function(response) {
	// 			$('#' + idContent).append(response);

	// 			setTimeout(function() {
	// 				if (typeof getPassData == 'function') {
	// 					getPassData($('meta[name="base_url"]').attr('content'), Cookies.get('arcaCookieSecurityCsrf'), dataArray);
	// 				} else {
	// 					console.log('function getPassData not initialize!');
	// 				}
	// 			}, 50);

	// 			// get form id
	// 			var formID = $('#' + idContent + ' > form').attr('id');
	// 			// > div:first-child

	// 			$("#" + formID)[0].reset(); // reset form
	// 			document.getElementById(formID).reset(); // reset form
	// 			$("#" + formID).attr('action', urlFunc); // set url

	// 			if (typeModal == 'modal') {
	// 				$('#generalTitle-' + sizeModal).text(title);
	// 				$('#generalModal-' + sizeModal).modal('show');
	// 				$("#" + formID).attr("data-modal", '#generalModal-' + sizeModal);
	// 			} else {
	// 				$('#offCanvasTitle-right').text(title);
	// 				$('#generaloffcanvas-right').offcanvas('toggle');
	// 				$("#" + formID).attr("data-modal", '#generaloffcanvas-right');
	// 			}

	// 			if (dataArray != null) {
	// 				$.each($('input, select ,textarea', "#" + formID), function(k) {
	// 					var type = $(this).prop('type');
	// 					var name = $(this).attr('name');

	// 					if (type == 'radio' || type == 'checkbox') {
	// 						$("input[name=" + name + "][value='" + dataArray[name] + "']").prop("checked", true);
	// 					} else {
	// 						$('#' + name).val(dataArray[name]);
	// 					}

	// 				});
	// 			}

	// 		}
	// 	});
	// }

	// function generateDatatable(id, typeTable = 'client', url = null, nodatadiv = 'nodatadiv', dataObj = null) {

	// 	const tableID = $('#' + id);
	// 	var table = tableID.DataTable().clear().destroy();

	// 	$.ajaxSetup({
	// 		data: {
	// 			'arcaSecurityCsrf': Cookies.get('arcaCookieSecurityCsrf') // csrf token
	// 		}
	// 	});

	// 	if (typeTable == 'client') {

	// 		return tableID.DataTable({
	// 			// "pagingType": "full_numbers",
	// 			'paging': true,
	// 			'ordering': true,
	// 			'info': true,
	// 			'lengthChange': true,
	// 			'responsive': false,
	// 			'language': {
	// 				"searchPlaceholder": 'Search...',
	// 				"sSearch": '',
	// 				"lengthMenu": '_MENU_ item / page',
	// 				"paginate": {
	// 					"first": "First",
	// 					"last": "The End",
	// 					"previous": "Previous",
	// 					"next": "Next"
	// 				},
	// 				"info": "Showing _START_ to _END_ of _TOTAL_ items",
	// 				"emptyTable": "No data is available in the table",
	// 				"info": "Showing _START_ to  _END_ of  _TOTAL_ items",
	// 				"infoEmpty": "Showing 0 to 0 of 0 items",
	// 				"infoFiltered": "(filtered from _MAX_ number of items)",
	// 				"zeroRecords": "No matching records",
	// 				"processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment..",
	// 				"loadingRecords": "Loading...",
	// 				"infoPostFix": "",
	// 				"thousands": ",",
	// 			},
	// 		});

	// 	} else {

	// 		if (dataObj != null) {
	// 			if (isObject(dataObj) || isArray(dataObj)) {
	// 				dataArr = {}; // {} will create an object
	// 				for (var key in dataObj) {
	// 					if (dataObj.hasOwnProperty(key)) {
	// 						dataArr[key] = dataObj[key];
	// 					}
	// 				}
	// 				dataArr['arcaSecurityCsrf'] = Cookies.get('arcaCookieSecurityCsrf') // csrf token
	// 				dataSent = dataArr;
	// 				// dataSent = new URLSearchParams(dataArr);
	// 			} else {
	// 				dataSent = new URLSearchParams({
	// 					'id': dataObj,
	// 					'arcaSecurityCsrf': Cookies.get('arcaCookieSecurityCsrf') // csrf token
	// 				});
	// 			}
	// 		} else {
	// 			dataSent = null;
	// 		}

	// 		// console.log('table data sent', dataSent);

	// 		if (dataSent == null) {
	// 			return tableID.DataTable({
	// 				// "pagingType": "full_numbers",
	// 				"processing": true,
	// 				"serverSide": true,
	// 				"responsive": true,
	// 				"iDisplayLength": 10,
	// 				"bLengthChange": true,
	// 				"searching": true,
	// 				"autoWidth": false,
	// 				"ajax": {
	// 					type: 'POST',
	// 					url: $('meta[name="base_url"]').attr('content') + url,
	// 					dataType: "JSON",
	// 					// data: dataSent,
	// 					headers: {
	// 						"Authorization": "Bearer " + Cookies.get('arcaCookieSecurityCsrf'),
	// 						'X-Requested-With': 'XMLHttpRequest',
	// 						'content-type': 'application/x-www-form-urlencoded',
	// 						"X-CSRF-TOKEN": Cookies.get('arcaCookieSecurityCsrf'),
	// 					},
	// 				},
	// 				"language": {
	// 					"searchPlaceholder": 'Search...',
	// 					"sSearch": '',
	// 					"lengthMenu": '_MENU_ item / page',
	// 					"paginate": {
	// 						"first": "First",
	// 						"last": "The End",
	// 						"previous": "Previous",
	// 						"next": "Next"
	// 					},
	// 					"info": "Showing _START_ to _END_ of _TOTAL_ items",
	// 					"emptyTable": "No data is available in the table",
	// 					"info": "Showing _START_ to _END_ of _TOTAL_ items",
	// 					"infoEmpty": "Showing 0 to 0 of 0 items",
	// 					"infoFiltered": "(filtered from _MAX_ number of items)",
	// 					"zeroRecords": "No matching records",
	// 					"processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment.. ",
	// 					"loadingRecords": "Loading...",
	// 					"infoPostFix": "",
	// 					"thousands": ",",
	// 				},
	// 				"columnDefs": [{
	// 					"render": function(data, type, row) {
	// 						return data;
	// 					},
	// 					"width": "14%",
	// 					"targets": -1,
	// 					"searchable": false,
	// 					"orderable": false
	// 				}],
	// 				initComplete: function() {

	// 					var totalData = this.api().data().length;

	// 					if (totalData > 0) {
	// 						$('#' + nodatadiv).hide();
	// 						$('#' + id + 'Div').show();
	// 					} else {
	// 						tableID.DataTable().clear().destroy();
	// 						$('#' + id + 'Div').hide();
	// 						$('#' + nodatadiv).show();
	// 					}

	// 				}
	// 			});
	// 		} else {
	// 			return tableID.DataTable({
	// 				// "pagingType": "full_numbers",
	// 				"processing": true,
	// 				"serverSide": true,
	// 				"responsive": true,
	// 				"iDisplayLength": 10,
	// 				"bLengthChange": true,
	// 				"searching": true,
	// 				"ajax": {
	// 					type: 'POST',
	// 					url: $('meta[name="base_url"]').attr('content') + url,
	// 					dataType: "JSON",
	// 					data: dataSent,
	// 					headers: {
	// 						"Authorization": "Bearer " + Cookies.get('arcaCookieSecurityCsrf'),
	// 						'X-Requested-With': 'XMLHttpRequest',
	// 						'content-type': 'application/x-www-form-urlencoded',
	// 						"X-CSRF-TOKEN": Cookies.get('arcaCookieSecurityCsrf'),
	// 					},
	// 				},
	// 				"language": {
	// 					"searchPlaceholder": 'Search...',
	// 					"sSearch": '',
	// 					"lengthMenu": '_MENU_ item / page',
	// 					"paginate": {
	// 						"first": "First",
	// 						"last": "The End",
	// 						"previous": "Previous",
	// 						"next": "Next"
	// 					},
	// 					"info": "Showing _START_ to _END_ of _TOTAL_ items",
	// 					"emptyTable": "No data is available in the table",
	// 					"info": "Showing _START_ to _END_ of _TOTAL_ items",
	// 					"infoEmpty": "Showing 0 to 0 of 0 items",
	// 					"infoFiltered": "(filtered from _MAX_ number of items)",
	// 					"zeroRecords": "No matching records",
	// 					"processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment.. ",
	// 					"loadingRecords": "Loading...",
	// 					"infoPostFix": "",
	// 					"thousands": ",",
	// 				},
	// 				initComplete: function() {

	// 					var totalData = this.api().data().length;

	// 					if (totalData > 0) {
	// 						$('#' + nodatadiv).hide();
	// 						$('#' + id + 'Div').show();
	// 					} else {
	// 						tableID.DataTable().clear().destroy();
	// 						$('#' + id + 'Div').hide();
	// 						$('#' + nodatadiv).show();
	// 					}

	// 				}
	// 			});
	// 		}

	// 	}
	// }
</script>