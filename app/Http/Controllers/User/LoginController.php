<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Maintenance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use SimpleXMLElement;

class LoginController extends Controller
{
    /*
        Berisi fungsi untuk: login user.
        API dipanggil di proyek mobile: RestClient.cs
            View: LoginAct.xaml
            View Model: LoginViewModel.cs
    */
    public function __construct()
    {
        ini_set('memory_limit',' 2048M'); //Memory limit ditambahkan karena json_encode get profile terkadang memory exhausted.
        $this->device = new Device();
        $this->maintenance = new Maintenance();
        $this->user = new User();
    }

    public function getamtarewardStatus(Request $request)
    {
        /*
		* Fungsi: Mendapat status maintenance aplikasi amtarewards
		 * Param:
		 *      - request:
		 *             - device    -> jenis device yang digunakan pengguna (android/ios)
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
		 *             - maintenance_info -> Status dan waktu terakhir memperbaharui status
         *             - device_info   -> jenis dan versi device 
		 */

        $device_param = $request->input('device');
        $device_info = $this->device->getDeviceVersion($device_param);
        $maintenance_info = $this->maintenance->getRecentStatusValue();

        if (!is_null($device_info) && !is_null($maintenance_info)) {
            $data['status'] = true;
            $data['message'] = 'Get Amtanesia status success!';
            $data['maintenance_info']['status'] = $maintenance_info->STATUS;
            $data['maintenance_info']['update_time'] = $maintenance_info->UPDATE_TIME;
            $data['device_info']['device'] = $device_info->DEVICE;
            $data['device_info']['version'] = $device_info->VERSION;
        } else {
            $data['status'] = false;
            $data['message'] = 'Get Amtanesia status failed!';
            $data['maintenance_info'] = '';
            $data['device_info'] = '';
        }

        return response($data, 200);
    }

    public function login(Request $request)
    {
         /*
		* Fungsi: login user
		 * Param:
		 *      - request:
		 *             - npm    -> ID User
         *             - password 
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
		 *             - api_key -> API Key yang terkait dengan device user.
		 */

        $data = $request->all();
       
        $validate = Validator::make($data, [
            'npm' => 'required',
            'password' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $npm = $data['npm'];
        $password = $data['password'];

        //Guzzle Client
        $client = new Client();
        $checkLoginResponse = $client->get($urlCheckLogin);

        $resultStringCheckLogin = $checkLoginResponse->getBody();
        $checkLogin = new SimpleXMLElement($resultStringCheckLogin);

        //Apabila NPM dan Password Benar
        if ($checkLogin == 'true') {
            //Get Profile
            $getProfileResponse = $client->get($urlGetProfile);
            $resultStringGetProfile = $getProfileResponse->getBody();
            $stringJsonResult = new SimpleXMLElement($resultStringGetProfile);
            $arrayResult = json_decode($stringJsonResult, true);

            $userExistData = $this->user->getUsersById($arrayResult['NPP']);

            //Apabila belum ada data mahasiswa di database users
            if (is_null($userExistData)) {

                if ($arrayResult['Foto_base64'] == null) {
                    $arrayResult['Foto_base64'] = $this->baseimage();
                }

                if ($arrayResult['Foto_base64'] == null) {
                    $arrayResult['Foto_base64'] = $this->baseimage();
                }

                $filePhoto = public_path('image_profile/' . $arrayResult['NPP'] . '.png');
                $filePhotoThumb = public_path('image_profile/' . $arrayResult['NPP'] . '_thumb.png');
                $isPhotoUpload = file_put_contents($filePhoto, base64_decode($arrayResult['Foto_base64']));
                $imgsize = getimagesizefromstring(base64_decode($arrayResult['Foto_base64']));
                if ($imgsize['mime'] == 'image/x-ms-bmp') {
                    $isPhotoUploadThumb = $this->saveBmpPhoto($arrayResult['Foto_base64'], $filePhotoThumb);
                } else {
                    $isPhotoUploadThumb = file_put_contents($filePhotoThumb, base64_decode($this->createThumbPhoto($arrayResult['Foto_base64'])));
                }
                 //return $filePhoto;

                if ($isPhotoUpload && $isPhotoUploadThumb) {
                    $userData = array(
                        "ID_USERS" => $arrayResult['NPP'],
                        "NAME" => $arrayResult['Nama'],
                        "PRODI" => $arrayResult['Prodi'],
                        "FAKULTAS" => $arrayResult['Fakultas'],
                        "POINTS" => 0,
                        "PHOTO" => url('') . '/image_profile/' . $arrayResult['NPP'] . '.png',
                        "PHOTO_THUMB" => url('') . '/image_profile/' . $arrayResult['NPP'] . '_thumb.png',
                        "CREATE_TIME" => Carbon::now(+7)->toDateTimeString(),
                        "TYPE"    => "student"
                    );

                    $data = User::create($userData);

                    $generatedApiKey = md5($arrayResult['Nama'] . $arrayResult['NPP'] . date('Y-m-d H:i:s') . 'amtanesia');
                    $newUserDeviceData = array(
                        "ID_USERS"        => $arrayResult['NPP'],
                        "API_KEY"         => $generatedApiKey,
                        "CREATE_TIME"     => date('Y-m-d H:i:s')
                    );

                    $new_device = $this->user->insertNewUserDevice($newUserDeviceData);

                    $data['status'] = true;
                    $data['message'] = 'Login success';
                    $data['api_key'] = $generatedApiKey;
                } else {
                    $data['status'] = false;
                    $data['message'] = 'Process login failed. Unable save profile picture. Please try again.';
                }
            } else {
                //Apabila sudah ada data mahasiswa di database users.   
                $apiKey = $this->user->getApiKey($userExistData->ID_USERS);
                $data['status'] = true;
                $data['message'] = 'Login success';
                $data['api_key'] = $apiKey;
            } 
        }
        else
			{
                //Login Dosen
                $passwordDosen = $data['password']; //Inisialisasi ulang karena yang algoritma mahasiswa pake urlencode, sementara algoritma dosen tidak.
				$checkTeacherLogin = $this->teacherCheckLogin($npm,$passwordDosen);
				if($checkTeacherLogin['status'])
				{
					if($checkTeacherLogin['login_result'] == 'true')
					{	
						$userExistData = $this->user->getUsersById($npm);

                        //Apabila tidak ada data dosen di database users
						if(empty($userExistData))
						{
							$teacherProfile = $this->teacherGetProfile($npm);
							if($teacherProfile['status'] == 'true')
							{
                                $filePhoto = public_path('image_profile/' . $teacherProfile['profile']['NPP'].'.png');
                                $filePhotoThumb = public_path('image_profile/' . $teacherProfile['profile']['NPP'].'_thumb.png');
								$isPhotoUpload = file_put_contents($filePhoto, base64_decode($teacherProfile['profile']['Foto_base64']));
								$imgsize = getimagesizefromstring(base64_decode($teacherProfile['profile']['Foto_base64']));
								if($imgsize['mime'] == 'image/x-ms-bmp'){
									$isPhotoUploadThumb = $this->saveBmpPhoto($teacherProfile['profile']['Foto_base64'], $filePhotoThumb);
								}else{
									$isPhotoUploadThumb = file_put_contents($filePhotoThumb, base64_decode($this->createThumbPhoto($teacherProfile['profile']['Foto_base64'])));
								}


								if($isPhotoUpload && $isPhotoUploadThumb)
								{
									$userData = array(
										"ID_USERS" => $teacherProfile['profile']['NPP'],
										"NAME" => $teacherProfile['profile']['NAMA'],
										"PRODI" => $teacherProfile['profile']['PRODI'],
										"FAKULTAS" => $teacherProfile['profile']['FAKULTAS'],
										"POINTS" => 0,
										"PHOTO" => url('') . '/image_profile/' . $teacherProfile['profile']['NPP'] . '.png',
										"PHOTO_THUMB" => url('') . '/image_profile/' . $teacherProfile['profile']['NPP'] . '_thumb.png',
										"CREATE_TIME" => Carbon::now(+7)->toDateTimeString(),
										"TYPE"	=> "teacher"
										);

									$this->user->insertNewUser($userData);

									$generatedApiKey = md5($teacherProfile['profile']['NAMA'] . $teacherProfile['profile']['NPP'] . date('Y-m-d H:i:s') .'amtanesia');
									$newUserDeviceData = array(
										"ID_USERS"		=> $teacherProfile['profile']['NPP'],
										"API_KEY" 		=> $generatedApiKey,
										"CREATE_TIME" 	=> date('Y-m-d H:i:s')
										);

									$this->user->insertNewUserDevice($newUserDeviceData);

									$data['status'] = true;
									$data['message'] = 'Login success';
									$data['api_key'] = $generatedApiKey;
								}
								else
								{
									$data['status'] = false;
									$data['message'] = 'Process login failed. Unable save profile picture. Please try again..';
								}

							}
							else
							{
								$data['status'] = false;
								$data['message'] = $teacherProfile['error_message'];
							}
						}
						else
						{
							$apiKey = $this->user->getApiKey($userExistData->ID_USERS);
							$data['status'] = true;
							$data['message'] = 'Login success';
							$data['api_key'] = $apiKey;
						}
					}
					else
					{
						$data['status'] = false;
						$data['message'] = 'Incorrect NPM or password!';
					}
				}
				else
				{
					$data['status'] = false;
					$data['message'] = $checkTeacherLogin['error_message'];
				}
			}

		echo json_encode($data);
    }

    public function teacherCheckLogin($npm, $password)
    {
        /* Untuk login dosen. Dipanggil pada fungsi login. */
		$data['status'] = false;
		$data['error_message'] = '';
		$data['login_result'] = '';
		
        //Login Dosen menggunakan asmx dan SOAP
		$xml_post_string = 
			'<?xml version="1.0" encoding="utf-8"?>
            <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
			  <soap12:Body>
			    <cekLogin xmlns="http://tempuri.org/">
			      <npm>'.$npm.'</npm>
			      <password>'.$password.'</password>
			      <key>Atm4nesia!</key>
			    </cekLogin>
			  </soap12:Body>
			</soap12:Envelope>';

		$headers = array(
            "POST /SIMKA_WS.asmx HTTP/1.1",
            "Content-Type: application/soap+xml; charset=utf-8",
            "Content-Length: ".strlen($xml_post_string)
        ); //SOAPAction: your op URL

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $soapUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch); 
		if(curl_errno($ch))
		{
		    // echo 'error:' . curl_error($ch);
		    $data['status'] = false;
		    $data['error_message'] = "".curl_error($ch);
		}
		else
		{
			$data['status'] = true;
		}

        curl_close($ch);

        if($data['status'] == true){
        	// $data['status'] = true;
        	// converting replace char "soap:Body" in the string $response with ""
	        $response1 = str_replace("<soap:Body>","",$response);
	        $response2 = str_replace("</soap:Body>","",$response1);
            
	        // convertingc to XML
	        $parser = simplexml_load_string($response2);
	        $data['login_result'] = (string) $parser->cekLoginResponse->cekLoginResult[0];
        }

        return $data;
	}

    public function teacherGetProfile($npm)
    {
        //Mendapat profile dosen
		$data['status'] = false;
		$data['error_message'] = '';
		$data['teacher_profile'] = '';
		
		$xml_post_string = 
			'<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
			  <soap12:Body>
			    <profileToJSON xmlns="http://tempuri.org/">
			      <npm>'.$npm.'</npm>
			      <key>Atm4nesia!</key>
			    </profileToJSON>
			  </soap12:Body>
			</soap12:Envelope>';

		$headers = array(
            "POST /SIMKA_WS.asmx HTTP/1.1",
            "Content-Type: application/soap+xml; charset=utf-8",
            "Content-Length: ".strlen($xml_post_string)     
        ); //SOAPAction: your op URL

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $soapUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch); 
		if(curl_errno($ch))
		{
		    // echo 'error:' . curl_error($ch);
		    $data['status'] = false;
		    $data['error_message'] = "".curl_error($ch);
		}
		else
		{
			$data['status'] = true;
		}
        curl_close($ch);

        if($data['status'] == false){

        }else{
        	// converting
	        $response1 = str_replace("<soap:Body>","",$response);
	        $response2 = str_replace("</soap:Body>","",$response1);

	        // convertingc to XML
	        $parser = simplexml_load_string($response2);
	        // var_dump($parser->profileToJSONResponse->profileToJSONResult->0);
	        $result = (string) $parser->profileToJSONResponse->profileToJSONResult[0];
	        // var_dump($result);
	        $resultJson = json_decode($result, true);
	        $data['profile'] = $resultJson;
        }

        return $data;
	}

    public function baseimage()
    {
        /* Untuk keperluan mengubah foto menjadi base64 */
        return '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wgARCAeABDgDASIAAhEBAxEB/8QAFwABAQEBAAAAAAAAAAAAAAAAAAECB//EABcBAQEBAQAAAAAAAAAAAAAAAAABAgP/2gAMAwEAAhADEAAAAeqAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACQALQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABJCKtlgLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAkRALNCaCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAkksqaUBQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQIlALQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgKAAAAAAAAAAAAAAABAVEigoAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJAAtAAAAAAAAAAAAAAAAggACigAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAkACgUAAAAAAAAAAAAAAACCAAKKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlgokC0AAAAAAAAAAAAJAtAAAggACigAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIBZQAAAAAAAAAAAAAiAKKAAAiyAFAKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAigAAAAAAAAAAAABLIAWUCgAAAJYiigAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEIFAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACCFlAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACFgKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAoICgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABCoKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQsIAUAoAAAAAAAAABKgKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAggAUCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIqIsFAKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACCKAKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEgUCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEshZQKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEAhSgAAAAAAABIAoAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABLIFAoAAAAAAABCAFlAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlgKgigCgAAAAAAAIIAooAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACWWBBZQKAAAAAAAASyABQKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAASolBKqUgKAAAAAAAASyAFlAoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABKAAAAAAAAAAAAEqJQCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAliKKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAllgKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAllgKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAllgKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlICgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEBQAAAAAAAAQFAASyKKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAiyKKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgiigAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAIooAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABLIAooAAAAAAAAAAAAAAAAAAAAAAAAAJAUKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAghZQKAAAAAAAAAAAAAAAAAAAAAAAACQAALQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIIUAoAAAAAAAAAAAAAAAAAAAAAAAAJAAtAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAASyAKKAAAAAAAAAAAAAAAAAAAAAAAACQAAAALQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABEWWUKAAAAAAAAAAAAAAAAAAAAAAABAiKApCFlAtAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACRKoFAAAAAAAAAAAAAAAAAAAAAAACQAQoBCCy2WULQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAkAC0AAAAAAAAAAAAAAAAAAAAAAJAACUC2Z1hm3Oi2WULQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAkC0AAAAAAAAAAAAAAAAAAAAAAAJAAABKmLm51rOotlmgtAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAlCgAAAAAAAAAAAAAAAAAAAAAAAkAAAZ1is5q41qWW2WaC0AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEACAtmblnNW5tmpqiULQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA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zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzjTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzywzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzDzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzxzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzDzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzCNfzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzgexfzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzys5d7zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzwMT3zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzykOdzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzjbzzzzzzzzzzzzzzzykj7b7zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzgNfzzzzzzzzzzzzzzzyj477TzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzgMN3zzzzzzzzzzzzzzzyz777zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzwCwfzzzzzzzzzzzzgPzzzj777zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzDiBzzzzzzzzzzzzw77zzzz76bzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyBRzzzzzzzzzzzzz767zzzyz7zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzx7rzzzzzwzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz67zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzrzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz7xbzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzQgSbzzzzzzzzzzzrzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyj57pzzzzzzzzzzzyTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzxzJ7bzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzbzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz57zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzy7rzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzb7zzzzzzzzzjzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzybzzzzzzzzy77bzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzZbzzzzzzzzx777zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzx7yp7zzzzzzzyj75zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyz667zzzzzzzzx77rzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzr7z7zzzzzzzzz777zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzTzzzzzzzzzzzzqbzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyjzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzXzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzTzzzzzzzzzzzzzzzzzzzzzzzyzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzBTzzzzzzzzzzzzzzzzzzzzzzyzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzwDTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzwRTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzy5zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyrzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyrzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzrzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzrzzzzzzzzzzzzzzzzzzzzzzzzzzzzzDzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzxzzzzzzzzzzzzzzzzzzzzzzzzjzzzxBDTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyyzzzyxDzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzTzzzzzziTzzzzgwzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzTzzzzzyDDzzyizzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzDzzzzzzzzzjzzyjzzjzzzRBTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzx3zzzzzzzzzzABzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyTzzyTzzzzyThzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzx3zzzzzzzzx3zyxbzzzzjhRTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyz7zzzyxzTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyD7zzzzTDzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzgz7zzzyRhzzzzzzzzzzzzzzzzzzzzzTzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyz77zzzzzzzzzzzzzzzzzzzzzzzzzwNbzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyj6rzzzxzzzzzzzzzzzzzzzzzzzzysNfzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzxD6LzzzzzzzzzzzzzzzzzzzzzzzzwMPzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyz77DzzzzzzzzzzzzzzzzzzzzzzzgMNesPzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzwWpTzzzzzzzzzzzzzzzzzzzzzzjWvNFuvzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzyNUzzzzzzzzzzzzzzzzzzzzzzzwMOceNGPzzzzz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';
    }

    public function saveBmpPhoto($bmp, $target)
    {
        /* Untuk menyimpan foto menjadi ekstensi BMP */
        $imgdata = "data://image/jpeg;base64," . $bmp;
        $imgresult = $this->imageCreateFromBmp($imgdata);
        ob_start();
        imagejpeg($imgresult);
        $image_data = ob_get_contents();
        ob_end_clean();
        $image_data_base64 = base64_encode($image_data);
        $upload = file_put_contents($target, base64_decode($this->createThumbPhoto($image_data_base64)));
        if ($upload) {
            return true;
        } else {
            return false;
        }
    }

    public function imageCreateFromBmp($p_sFile)
    {
        /* Untuk menyimpan foto menjadi ekstensi BMP */
        $file = fopen($p_sFile, "rb");
        $read = fread($file, 10);
        while (!feof($file) && ($read <> "")) $read .= fread($file, 1024);

        $temp = unpack("H*", $read);
        $hex = $temp[1];
        $header = substr($hex, 0, 108);

        if (substr($header, 0, 4) == "424d") {
            $header_parts = str_split($header, 2);
            $width =  hexdec($header_parts[19] . $header_parts[18]);
            $height = hexdec($header_parts[23] . $header_parts[22]);
            unset($header_parts);
        }

        $x = 0;
        $y = 1;
        $image = imagecreatetruecolor($width, $height);
        $body = substr($hex, 108);
        $body_size = (strlen($body) / 2);
        $header_size = ($width * $height);
        $usePadding = ($body_size > ($header_size * 3) + 4);

        for ($i = 0; $i < $body_size; $i += 3) {
            if ($x >= $width) {
                if ($usePadding)
                    $i    +=    $width % 4;
                $x    =    0;
                $y++;
                if ($y > $height)
                    break;
            }
            $i_pos    =    $i * 2;
            $r        =    hexdec($body[$i_pos + 4] . $body[$i_pos + 5]);
            $g        =    hexdec($body[$i_pos + 2] . $body[$i_pos + 3]);
            $b        =    hexdec($body[$i_pos] . $body[$i_pos + 1]);
            $color    =    imagecolorallocate($image, $r, $g, $b);
            imagesetpixel($image, $x, $height - $y, $color);
            $x++;
        }

        unset($body);
        return $image;
    }

    public function createThumbPhoto($data)
    {
        /* Membuat foto thumbnail (foto dengan resolusi lebih rendah) */
        $img64 = base64_decode($data);
        $img = imagecreatefromstring($img64);
        $percent = 0.3;
        list($width, $height) = getimagesizefromstring($img64);
        $newwidth = $width * $percent;
        $newheight = $height * $percent;
        $thumb = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($thumb, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        ob_start();
        imagejpeg($thumb);
        $content = ob_get_contents();
        ob_end_clean();
        return base64_encode($content);
    }
}
