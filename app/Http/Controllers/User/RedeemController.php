<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\Periode;
use App\Models\Redeem;
use App\Models\User;
use App\Models\Catalogue;
use App\Models\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RedeemController extends Controller
{
    /*
        Berisi fungsi untuk: menampilkan data katalog di mobile.
        API dipanggil di proyek mobile: RestClient.cs
            View: DetailCatalogueAct.xaml
            View Model: CatalogueViewModel.cs
    */
    public function __construct()
    {
        $this->amtanesia = new Amtanesia();
        $this->user = new User();
        $this->periode = new Periode();
        $this->redeem = new Redeem();
        $this->catalogue = new Catalogue();
        $this->helper = new Helper();
    }

    public function getRedeemUser(Request $request){
          /*
		* Fungsi: Mendapatkan data redeem per user. 
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL untuk autentikasi pengguna
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
         *             - items       -> data redeem yang akan ditampilkan
		 */
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

        if($resultApiKey['status'])
        {
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
            $idUser = $resultProfile->ID_USERS;
            $activePeriode = $this->periode->getActivePeriode();
            $resultRedeemUser = $this->redeem->getRedeemUser($idUser,$activePeriode);
           
            if(count($resultRedeemUser) > 0 )
            {
                foreach ($resultRedeemUser as $key => $value) {
                    $redeemUser[$key]['id_redeem'] = $value->ID_REDEEM_LOG;
                    $redeemUser[$key]['item_name'] = $value->NAME_CATALOGUE;
                    $redeemUser[$key]['date_redeem'] = $value->REDEEM_TIME;
                    $redeemUser[$key]['status_redeem'] = $value->REDEEM_STATUS;
                    $redeemUser[$key]['key_redeem'] = $value->REDEEM_KEY;
                    $photo['small_url'] = $value->PHOTO_SMALL;
                    $photo['medium_url'] = $value->PHOTO_MEDIUM;
                    $photo['large_url'] = $value->PHOTO_LARGE;
                    $redeemUser[$key]['item_photo'] = $photo;
                }
                
                $data['status'] = true;
                $data['message'] = 'Get redeem user success!';
                $data['items'] = $redeemUser;
            }
            else
            {
                $data['status'] = true;
                $data['message'] = 'Get redeem user success!';
                $data['items'] = [];
            }

        }
        else
        {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        //echo json_encode($data);
        return $data;
    }


    public function userRedeem(Request $request){
          /*
		* Fungsi: Digunakan saat user melakukan redeem. 
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL untuk autentikasi pengguna
         *              -id_catalogue -> ID catalogue yang diredeem
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
		 */
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

        if($resultApiKey['status'])
        {
            $submitData = $request->all();
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
            $idUser = $resultProfile->ID_USERS;
            $pointUser = $resultProfile->POINTS;

            $validate = Validator::make($submitData, [
                'id_catalogue'          => 'required'
            ]);

            if ($validate->fails()) {
                $data['status'] = false;
                $data['message'] = 'Data input problem!';
                return response(['message' => $validate->errors(), 'data' => $data], 400); //return error invalid input
            }
            
            $redeemRemaining = $this->getUserRedeemRemaining($idUser);

            if($redeemRemaining != 0)
            {
                $idCatalogue = $submitData['id_catalogue'];
                $catalogue = $this->catalogue->getCatalogById($idCatalogue);//dari model catalogue mendapat item katalog yang diklik.
                $stock = $catalogue[0]->STOCK;
                $pointReq = $catalogue[0]->POINT_REQ;
                $max_redeem = $catalogue[0]->CTG_MAX_REDEEM;
                $pointTotal = $pointUser - $pointReq;
                $redeemedCatalaog = $this->redeem->getRedeemedCatalog($idCatalogue, $idUser);//Mendaftarkan item yang diredeem oleh user ke dalam model redeem

                $isValidToRedeem = false;
                $isValidToRedeemGrandPrize = true;

                if($max_redeem == 'unlimited'){
                    $isValidToRedeem = true;
                }else{
                    if(count($redeemedCatalaog) >= $max_redeem){
                        $isValidToRedeem = false;
                    }else{
                        if($catalogue[0]->ID_CTG_TYPE == 1) // grand prize :: default config
                        {
                            $isValidToRedeemGrandPrize = $this->isUserAlreadyReedemGrandprizeLastPeriode($idUser);
                            if($isValidToRedeemGrandPrize)
                                $isValidToRedeem = true;
                            else
                                $isValidToRedeem = false;
                        }
                        else
                            $isValidToRedeem = true;
                            
                    }
                }

                if($stock != '0')
                {
                    if($isValidToRedeem)
                    {
                        if($pointTotal > 0){
                            $redeemKey = md5($apiKey . $idCatalogue . date('Y-m-d H:i:s') .'amtanesia');
                            $redeemData = array(
                                'ID_CATALOGUE'      => $idCatalogue,
                                'ID_USERS'          => $idUser,
                                'ID_REDEEM_STATUS'  => 1,
                                'REDEEM_KEY'        => $redeemKey,
                                'REDEEM_TIME'       => date('Y-m-d H:i:s')
                            );

                            if($this->redeem->redeemByUser($redeemData, $idCatalogue, $stock, $idUser, $pointTotal))
                            {
                                $data['status'] = true;
                                $data['message'] = 'Redeem success!';
                                $data['redeem_key'] = $redeemKey;
                            }
                            else
                            {
                                $data['status'] = false;
                                $data['message'] = 'Error happened!';
                            }
                        }
                        else
                        {
                            $data['status'] = false;
                            $data['message'] = 'Your point is not enough to redeem this item!';
                        }
                    }
                    else
                    {
                        $data['status'] = false;
                            
                        if($isValidToRedeemGrandPrize == false)
                        {
                            $data['message'] = 'Sorry, you have redeemed a Grand prize item in the last period..';
                        }
                        else
                        {
                            $data['message'] = 'Sorry, this item can only redeemed as much as '.$max_redeem.' times!';
                        }
                    }
                }
                else
                {
                    $data['status'] = false;
                    $data['message'] = 'This item out of stock!';
                }
            }
            else
            {
                $data['status'] = false;
                $data['message'] = 'Sorry, you have reached redeem quota..';
            }
        }
        else
        {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }
        return $data;
    }

    public function getUserRedeemRemaining($idUser)
    {
        /** 
         * Fungsi: Mendapat jumlah redeem user tertentu pada periode tertentu.
         * Param:
         *      - idUser            -> ID User yang akan dicari
         * Return:
         *      - userRedeemRemaining -> Sisa jumlah redeem.
        */
        $activePeriode = $this->periode->getActivePeriode();
        $userTotalRedeem = $this->redeem->getRedeemUser($idUser, $activePeriode);
        $maxRedeem = $this->helper->getHelperValue('MAX_REDEEM');
        $userRedeemRemaining = $maxRedeem->VALUE - count($userTotalRedeem);
        return $userRedeemRemaining;
    }

    public function isUserAlreadyReedemGrandprizeLastPeriode($userid)
    {
        /** 
         * Fungsi: Mengecek apakah user sudha pernah redeem grand prize pada periode sebelumnya
         * Param:
         *      - idUser            -> ID User yang akan dicari
         * Return:
         *      - true or false
        */
        $valid = false;
        $allPeriodes = $this->periode->getAllPeriode();//Mencari semua id periode
        if(count($allPeriodes) > 0)
        {
            if(count($allPeriodes) == 1)
                $valid = true;
            else
            {
                $temp_i = 0;
                $activePeriode = $this->periode->getActiveYearPeriode();//Mencari periode yang sedang aktif
                for ($i=0; $i < count($allPeriodes) ; $i++) { 
                    if($allPeriodes[$i]->ID_PERIOD == $activePeriode->ID_PERIOD)
                        $temp_i = $i;
                }

                if($temp_i == 0)
                    $valid = true;
                else
                {
                    $periodBeforeActive = $this->periode->getPeriodeById($allPeriodes[$temp_i-1]->ID_PERIOD);//Mencari periode sebelum yang sekarang sedang aktif
                    $grandPrizeRedeemed = $this->redeem->getUserGrandprizeRedeemed($userid, $periodBeforeActive->ID_PERIOD);
                    count($grandPrizeRedeemed) > 0 ? $valid = false : $valid = true;      
                }
            }
        }
        else
        {
            $valid = false;
        }

        return $valid;
    }
}
