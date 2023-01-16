<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\User;
use App\Models\Catalogue;
use App\Models\Periode;
use App\Models\Redeem;
use App\Models\Helper;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    /*
        Berisi fungsi untuk: menampilkan data katalog di mobile.
        API dipanggil di proyek mobile: RestClient.cs
            View: CatalogueAct.xaml
            View Model: CatalogueViewModel.cs
    */
    public function __construct()
    {
        $this->amtanesia = new Amtanesia();
        $this->catalogue = new Catalogue();
        $this->user = new User();
        $this->periode = new Periode();
        $this->redeem = new Redeem();
        $this->helper = new Helper();
    }

    public function getCatalogue(Request $request)
    {   
        /** 
         * Fungsi: Mendapat semua data katalog.
         * Param:
         *      - request:
         *             - api_key    -> API key yang ada di parameter URL 
         * Return:
         *      - data -> Array:
         *             - status     -> Status API Key valid atau tidak
         *             - message    -> Pesan pemanggilam API
         *             - catalogues -> Data katalog  
        */

        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
        if($resultApiKey['status'])
        {

        	$data['status'] = true;
        	$data['message'] = 'Get catalogue success!';
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
            $idUser = $resultProfile->ID_USERS;
        	$resultCatalogue = $this->catalogue->getAllCatalogue($this->periode->getActivePeriode(), $resultProfile->TYPE);
            $redeemRemaining = $this->getUserRedeemRemaining($idUser);
            if(count($resultCatalogue) > 0)
            {
                $data['redeem_remaining'] = $redeemRemaining;

                for ($i=0; $i < count($resultCatalogue); $i++) { 
                    $catalogueData[$i]['catalogue_id'] = $resultCatalogue[$i]->ID_CATALOGUE;
                    $catalogueData[$i]['catalogue_title'] = $resultCatalogue[$i]->NAME_CATALOGUE;
                    $catalogueData[$i]['catalogue_info'] = $resultCatalogue[$i]->DESCRIPTION;
                    $catalogueData[$i]['catalogue_point'] = $resultCatalogue[$i]->POINT_REQ;
                    $catalogueData[$i]['catalogue_stock'] = $resultCatalogue[$i]->STOCK;

                    $cat_type['id_cat_type'] = $resultCatalogue[$i]->ID_CTG_TYPE;
                    $cat_type['name_cat_type'] = $resultCatalogue[$i]->CTG_TYPE;
                    $cat_type['max_redeem_cat_type'] = $resultCatalogue[$i]->CTG_MAX_REDEEM;
                    $catalogueData[$i]['catalogue_type'] =  $cat_type;

                    $photo['small_url'] = $resultCatalogue[$i]->PHOTO_SMALL;
                    $photo['medium_url'] = $resultCatalogue[$i]->PHOTO_MEDIUM;
                    $photo['large_url'] = $resultCatalogue[$i]->PHOTO_LARGE;
                    $catalogueData[$i]['catalogue_photo'] = $photo;
                }
                $data['catalogues'] = $catalogueData;
            }
        	else
            {
                $data['catalogues'] = [];
            }
        }
        else
        {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        return $data;
    }

    public function getUserRedeemRemaining($idUser){
        /** 
         * Fungsi: Mendapat jumlah redeem user tertentu pada periode tertentu.
         * Param:
         *      - idUser            -> ID User yang akan dicari
         * Return:
         *      - userRedeemRemaining -> Sisa jumlah redeem.
        */
        $activePeriode = $this->periode->getActivePeriode(); //dari Model Periode. Mendapat data periode aktif.
        $userTotalRedeem = $this->redeem->getRedeemUser($idUser, $activePeriode); //dari Model Redeem. Mendapat daftar redeem user tertentu pada periode tertentu.
        $maxRedeem = $this->helper->getHelperValue('MAX_REDEEM'); // dari Model Helper. Mendapat jumlah maksimum redeem.
        $userRedeemRemaining = $maxRedeem->VALUE - count($userTotalRedeem); // mengurangi jumlah maksimum redeem dengan jumlah data redeem user. Mendapat sisa jumlah redeem. 
        return $userRedeemRemaining;
    }
}
