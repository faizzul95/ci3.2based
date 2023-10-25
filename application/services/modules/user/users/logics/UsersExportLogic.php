<?php

namespace App\services\modules\user\users\logics;

use App\services\modules\user\users\processors\UsersSearchProcessors;

class UsersExportLogic
{
    public function __construct()
    {
    }

    public function logic($request = NULL)
    {
        if (hasData($request, 'type')) {
            if ($request['type'] == 'print')
                return $this->printAsPDF($request);
            else if ($request['type'] == 'excel')
                return $this->exportToExcel($request);
        }
    }

    private function exportToExcel($request)
    {
        $condition['id !='] = 1; // remove superadmin acc
        
        if (hasData($request, 'filter')) {
            $condition['user_gender'] = hasData($request, 'filter.gender_filter', true);
            $condition['user_status'] = hasData($request, 'filter.status_filter', true);
            removeNullorEmptyValues($condition); // will remove empty filter
        }

        $data = app(new UsersSearchProcessors)->execute([
            'fields' => 'id,name,user_nric,email,user_contact_no,user_gender,user_status',
            'conditions' => $condition
        ], 'get_all');

        if (hasData($data)) {
            $columnToExportString = hasData($request, 'filter.column_filter', true);
            $columnToExportArr = array_merge(
                ['name'], // put important column to export, required atleast 1 column
                hasData($columnToExportString) ? explode(',', $columnToExportString) : []
            );

            $mappingColumn = [
                'name' => ['header' => 'Nama Pegawai', 'column' => 'name'],
                'user_nric' => ['header' => 'No. Kad Pengenalan', 'column' => 'user_nric'],
                'user_dob' => ['header' => 'Tarikh Lahir', 'column' => 'user_dob'],
                'email' => ['header' => 'email', 'column' => 'email'],
                'user_contact_no' => ['header' => 'No Telefon', 'column' => 'user_contact_no'],
                'user_gender' => ['header' => 'Jantina', 'column' => 'user_gender_name'], // custom column (use append attribute) 
                'user_status' => ['header' => 'Status', 'column' => 'user_status_name'],
            ];

            // reset @ initialize data
            $dataHeader[0] = []; // header always at index 0
            $dataToExport = [];

            foreach ($columnToExportArr as $value) {
                if (isset($mappingColumn[$value])) {
                    array_push($dataHeader[0], $mappingColumn[$value]['header']);
                }

                foreach ($data as $key => $row) {
                    $columnName = $mappingColumn[$value]['column'];
                    $dataToExport[$key][] = purify($row[$columnName]);
                }
            }

            $exportData = array_merge($dataHeader, $dataToExport);

            // remember the files name need to add file extension ".xls"
            return exportToExcel($exportData, 'senarai_pegawai.xls');
        } else {
            return ['code' => 422, 'message' => 'Tiada maklumat pegawai ditemui'];
        }
    }

    private function printAsPDF($request)
    {
        $condition['id !='] = 1; // remove superadmin acc

        if (hasData($request, 'filter')) {
            $condition['user_gender'] = hasData($request, 'filter.gender_filter', true);
            $condition['user_status'] = hasData($request, 'filter.status_filter', true);
            removeNullorEmptyValues($condition); // will remove empty filter
        }

        $data = app(new UsersSearchProcessors)->execute([
            'fields' => 'id,name,user_nric,email,user_contact_no,user_gender,user_status',
            'conditions' => $condition
        ], 'get_all');

        if (hasData($data)) {

            $header = '<p>
					<table cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td colspan="2">
							<table cellspacing="0" cellpadding="0">
								<tr>
									<td width="15%">
                                        <br>
										<img width="65px" src="' . getImageSystemLogo() . '" class="img-fluid mt-3">
									</td>
									<td width="85%">
                                        &nbsp&nbsp ' . strtoupper(env('COMPANY_NAME')) . ' <br>	
										&nbsp&nbsp ALAMAT <br>	
										&nbsp&nbsp POSKod Pengawai BANDAR, NEGERI, MALAYSIA
									</td>
								</tr>
							</table>
						</td>
						<td colspan="2" align="center" style="font-size:90%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SENARAI PEGAWAI </td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
					</table>
				</p>';

            $body = '<table border="1" cellpadding="0" cellspacing="0" class="table table-bordered table-striped" width="100%">';
            $body .= '<thead class="table-dark">
		                    <th style="width:5%;font-size: 13px;"> NO. </th>
		                    <th style="width:43%;font-size: 13px;"> NAMA </th>
		                    <th style="width:24%;font-size: 13px;"> KAD PENGENALAN </th>
		                    <th style="width:18%;font-size: 13px;"> EMAIL </th>
		                 </thead>';

            $no = 1;
            foreach ($data as $row) {
                $name = purify($row['name']);
                $nric = purify($row['user_nric']);
                $email = purify($row['email']);
                $phone = purify($row['user_contact_no']);

                $body .= '<tr>';
                $body .= '<td style="height:20px;font-size:12px;"><center>' . $no++ . '</center></td>';
                $body .= '<td style="height:20px;font-size:12px;"> &nbsp; ' . truncateText($name, 50) . '</td>';
                $body .= '<td style="height:20px;font-size:12px;"><center>' . $nric . '</center></td>';
                $body .= '<td style="height:20px;font-size:12px;"><center>' . $email . '</center></td>';
                $body .= '</tr>';
            }
            $body .= '</table>';

            $footer = '';
            $dataToPrint = $header . $body . $footer;

            return ['code' => 200, 'message' => 'Cetak senarai pegawai', 'result' => $dataToPrint];
        } else {
            return ['code' => 422, 'message' => 'Senarai pegawai gagal dicetak'];
        }
    }
}
