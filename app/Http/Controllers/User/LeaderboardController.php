<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
	/*
        Berisi fungsi untuk: menampilkan data leaderboard (TOP 10 poin tertinggi).
        API dipanggil di proyek mobile: RestClient.cs
            View: LeaderboardAct.xaml
            View Model: LeaderboardViewModel.cs
    */
	public function __construct()
	{
		$this->amtanesia = new Amtanesia();
		$this->user = new User();
	}

	public function getLeaderboard($leaderboard, $resultProfile)
	{
		/*
		* Fungsi: Mendapat data leaderboard (TOP 10 poin terbanyak) dan data poin user.
		 * Param:
		 *      - leaderboard	-> data top 10
		 * 		- resultProfile -> Data profile user
		 * Return:
		 *      - data -> Array:
		 *         		-my_rank -> data user beserta point dan rank.
		 */
		$itemLeaderboard = array();
		//Data leaderboard
		if (count($leaderboard) > 0) {
			for ($i = 0; $i < count($leaderboard); $i++) {

				$itemLeaderboard['rank'] = $leaderboard[$i]->rank;
				$itemLeaderboard['point'] = $leaderboard[$i]->POINTS;
				$itemLeaderboard['student_id'] = $leaderboard[$i]->ID_USERS;
				$itemLeaderboard['student_name'] = $leaderboard[$i]->NAME;
				$itemLeaderboard['student_faculty'] = $leaderboard[$i]->FAKULTAS;
				$itemLeaderboard['student_prodi'] = $leaderboard[$i]->PRODI;
				$itemLeaderboard['student_photo'] = $leaderboard[$i]->PHOTO_THUMB;

				$data['leaderboard'][] = $itemLeaderboard;
			}
		} else {
			$data['leaderboard'] = [];
		}
		//Data user yang login
		$myRank['rank'] = $this->user->getUserRank($resultProfile->ID_USERS, $resultProfile->TYPE);
		$myRank['point'] = $resultProfile->POINTS;
		$myRank['student_id'] = $resultProfile->ID_USERS;
		$myRank['student_name'] = $resultProfile->NAME;
		$myRank['student_faculty'] = $resultProfile->FAKULTAS;
		$myRank['student_prodi'] = $resultProfile->PRODI;
		$myRank['student_photo'] = $resultProfile->PHOTO;

		$data['my_rank'] = $myRank;

		return $data;
	}

	public function getLeaderboardTeacher(Request $request)
	{	
		//Leaderboard untuk dosen
		$apiKey = $request->input('api_key');
		$resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
		if ($resultApiKey['status']) {

			$resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);

			$leaderboard = $this->user->getUserLeaderboardTeacher();
			$data = $this->getLeaderboard($leaderboard, $resultProfile);
			$data['status'] = true;
			$data['message'] = 'Get leaderboard success!';
		} else {
			$data['status'] = false;
			$data['message'] = 'Api Key is not valid!';
		}
		echo json_encode($data);
		// return $data;
	}

	public function getLeaderboardStudent(Request $request)
	{
		//Leaderboard untuk mahasiswa
		$apiKey = $request->input('api_key');
		$resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
		if ($resultApiKey['status']) {

			$resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);

			$leaderboard = $this->user->getUserLeaderboardStudent();
			$data = $this->getLeaderboard($leaderboard, $resultProfile);
			$data['status'] = true;
			$data['message'] = 'Get leaderboard success!';
		} else {
			$data['status'] = false;
			$data['message'] = 'Api Key is not valid!';
		}

		// echo json_encode($data);
		return $data;
	}
}
