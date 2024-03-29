<?php

// if (isset($_POST['fileName'])) {
//     $filename = $_POST['fileName'];
//     $data = isset($_POST['dataArray']) ? $_POST['dataArray'] : '';
//     $filePath = "../../../application/views/$filename";

//     if (file_exists($filePath)) {
//         $opts = [
//             'http' => [
//                 'method' => 'POST',
//                 'header' => 'Content-Type: application/x-www-form-urlencoded',
//                 'content' => !empty($data) ? http_build_query($data) : null,
//             ],
//         ];

//         $context = stream_context_create($opts);
//         echo file_get_contents($filePath, false, $context);
//     } else {
//         // echo "File does not exist.";
//         echo '<div class="alert alert-danger" role="alert">
//                 File <b><i>' .
//             $filePath .
//             '</i></b> does not exist.
//                </div>';
//     }
// }

?>

<script>
    function loadFileContent(fileName, idToLoad, sizeModal = 'lg', title = 'Default Title', dataArray = null,
        typeModal = 'modal') {

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
                        getPassData($('meta[name="base_url"]').attr('content'), Cookies.get(
                            csrf_cookie_name), dataArray);
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

                    setTimeout(async function() {
                        $('#offCanvasTitle-right').text(title);
                        $('#generaloffcanvas-right').offcanvas('toggle');
                        $('.custom-width').css('width', sizeModal);
                    }, 10);
                }
            }
        });
    }

    function loadFormContent(fileName, idToLoad, sizeModal = 'lg', urlFunc = null, title = 'Default Title', dataArray =
        null, typeModal = 'modal') {

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
                        getPassData($('meta[name="base_url"]').attr('content'), Cookies.get(
                            csrf_cookie_name), dataArray);
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
                            $("input[name=" + name + "][value='" + dataArray[name] + "']").prop(
                                "checked", true);
                        } else {
                            $('#' + name).val(dataArray[name]);
                        }

                    });
                }

            }
        });
    }
</script>
