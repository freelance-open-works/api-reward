<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\ElearningChallenge;
use App\Models\History;
use App\Models\Periode;
use App\Models\User;
use ArrayObject;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
	/*
        Berisi fungsi untuk: menampilkan data history di mobile.
        API dipanggil di proyek mobile: RestClient.cs
            View: HistoryAct.xaml
            View Model: HistoryViewModel.cs
    */
	public function __construct()
	{
		$this->amtanesia = new Amtanesia();
		$this->user = new User();
		$this->history = new History();
		$this->elearning = new ElearningChallenge();
		$this->periode = new Periode();
	}

	public function getHistoryUser(Request $request)
	{
		/** 
		 * Fungsi: Mendapat data history keikutsertaan event dari user tertentu
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL 
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
		 *             - histories -> Data history  
		 */
		$apiKey = $request->input('api_key');
		$resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
		$data = new ArrayObject();
		if ($resultApiKey['status']) {
			$resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
			$idUser = $resultProfile->ID_USERS;
			$resultHistory = $this->history->getHistoryUser($idUser);

			$data['status'] = true;
			$data['message'] = 'Get history user success!';

			$historyData = array();
			$temp = array();

			if (count($resultHistory) > 0) {
				$j = -1;
				$k = 0;
				for ($i = 0; $i < count($resultHistory); $i++) {

					if (!in_array($resultHistory[$i]->ID_EVENTS, $temp)) {
						array_push($temp, $resultHistory[$i]->ID_EVENTS);
						$j++;
						$k = 0;

						$historyData['history_id'] = $resultHistory[$i]->ID_HISTORY;
						$historyData['history_title'] = $resultHistory[$i]->NAME_EVENTS;
						$historyData['history_date'] = $resultHistory[$i]->DATE_HISTORY;

						$photo['small_url'] = $resultHistory[$i]->PHOTO_SMALL;
						$photo['medium_url'] = $resultHistory[$i]->PHOTO_MEDIUM;
						$photo['large_url'] = $resultHistory[$i]->PHOTO_LARGE;
						$historyData['history_photo'] = $photo;
						$historyData['point_reached'] = $resultHistory[$i]->POINT_REACHED;
						$data['histories'][] = $historyData;
						$k++;
					} else {
						$data['histories'][$j]['point_reached'] = $data['histories'][$j]['point_reached'] + $resultHistory[$i]->POINT_REACHED;
						$k++;
					}
				}
			} else {
				$data['histories'] = [];
			}
		} else {
			$data['status'] = false;
			$data['message'] = 'Api Key is not valid!';
		}

		echo json_encode($data);
	}

	public function getElearningHistoryUser(Request $request)
	{
		/** 
		 * Fungsi: Mendapat data elearning history dari user tertentu
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL 
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
		 *             - eLearning_histories -> Data e-learning history  
		 */

		$apiKey = $request->input('api_key');
		$resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

		if ($resultApiKey['status']) {
			$data['status'] = true;
			$data['message'] = 'Get E-Learning History User success!';

			$resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);

			$resultElearningHistory = $this->elearning->getAllElearningHistoryByUserId($resultProfile->ID_USERS);
			$resultAllElearnChall = $this->elearning->getAllElearningChallenge($this->periode->getActivePeriode(), $resultProfile->TYPE);

			if (count($resultElearningHistory) > 0 && count($resultAllElearnChall) > 0) {
				for ($i = 0; $i < count($resultAllElearnChall); $i++) {
					$totalPoint = 0;

					for ($j = 0; $j < count($resultElearningHistory); $j++) {

						if ($resultAllElearnChall[$i]->ID_ELEARNING_CHALLENGE == $resultElearningHistory[$j]->ID_ELEARNING_CHALLENGE) {
							$totalPoint = $resultElearningHistory[$j]->TOTAL_POINT;
						}
					}
					$eLearningHistory['id_elearning_history'] = $resultAllElearnChall[$i]['ID_ELEARNING_CHALLENGE'];
					$eLearningHistory['name_elearning_history'] = $resultAllElearnChall[$i]['NAME'];
					$eLearningHistory['desc_elearning_history'] = $resultAllElearnChall[$i]['DESC'];
					$eLearningHistory['total_point_elearning_history'] = $totalPoint;
					$data['eLearning_histories'][$i] = $eLearningHistory;
				}
			} else {
				$data['eLearning_histories'] = [];
			}
		} else {
			$data['status'] = false;
			$data['message'] = 'Api Key is not valid!';
		}

		return $data;
	}

	public function getItemElearningHistoryByIdChall(Request $request)
	{

		/*
		* Fungsi: Mendapat data elearning history dari user tertentu yang dikelompokkan berdasarkan ID Challenge.
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL 
		 *  			- id_elearning_history -> ID E-Learning history yang diambil
		 * 				- id_elearning_challenge - > ID e-learning challenge yang diambil
		 * 				- name -> Nama E-learning Challenge
		 * 				- point -> jumlah point yang didapat
		 * 				- info -> nama mata kuliah
		 * 				- IP address -> IP address yang melakukan challenge
		 * 				- data_history -> tanggal melakukan challenge
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
		 *             - eLearning_histories -> Data e-learning history  
		 */
		$getData = $request->all();

		$apiKey = $request['api_key'];
		$resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

		if ($resultApiKey['status']) {
			$data['status'] = true;
			$data['message'] = 'Get eLearning history user success!';

			$resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
			$idChall = $getData['id_echall'];
			$resultItemHistory = $this->elearning->getItemElearningHistoryByIdChall($idChall, $resultProfile->ID_USERS);

			if (count($resultItemHistory) > 0) {
				for ($i = 0; $i < count($resultItemHistory); $i++) {
					$eLearningHistoryItem['id_elearning_history'] = $resultItemHistory[$i]->ID_ELEARNING_HISTORY;
					$eLearningHistoryItem['id_elearning_challenge'] = $resultItemHistory[$i]->ID_ELEARNING_CHALLENGE;
					$eLearningHistoryItem['name'] = $resultItemHistory[$i]->NAME;
					$eLearningHistoryItem['desc'] = $resultItemHistory[$i]->DESC;
					$eLearningHistoryItem['point'] = $resultItemHistory[$i]->POINT;
					$eLearningHistoryItem['info'] = $resultItemHistory[$i]->INFO;
					$eLearningHistoryItem['ip_address'] = $resultItemHistory[$i]->IP_ADDRESS;
					$eLearningHistoryItem['date_history'] = $resultItemHistory[$i]->DATE_HISTORY;

					$data['elearning_history_item'][$i] = $eLearningHistoryItem;
				}
			} else {
				$data['elearning_history_item'] = [];
			}
		} else {
			$data['status'] = false;
			$data['message'] = 'Api Key is not valid!';
		}

		echo json_encode($data);
	}
}
