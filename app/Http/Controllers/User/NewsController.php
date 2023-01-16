<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\News;
use App\Models\Periode;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    
    /*
        Berisi fungsi untuk: menamppilkan berita.
        API dipanggil di proyek mobile: RestClient.cs
            View: NewsAct.xaml
            View Model: NewsViewModel.cs
    */

    public function __construct()
    {
        $this->amtanesia = new Amtanesia();
        $this->news = new News();
        $this->periode = new Periode();
    }

    public function getAllNews(Request $request)
    {
          /*
		* Fungsi: Mendapatkan semua berita. 
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL untuk autentikasi pengguna
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
         *             - news       -> data berita yang akan ditampilkan
		 */
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

        if($resultApiKey['status'])
        {
        	$data['status'] = true;
        	$data['message'] = 'Get news success!';

            $periode = $this->periode->getActiveYearPeriode();

            $start = $periode->YEAR_START;
            $finish = $periode->YEAR_FINISH;

        	$resultNews = $this->news->getNewsByPeriode($start, $finish);

            if(count($resultNews) > 0)
            {
                for ($i=0; $i < count($resultNews); $i++) { 
                    $newsData[$i]['news_id'] = $resultNews[$i]->ID_NEWS;
                    $newsData[$i]['news_title'] = $resultNews[$i]->NEWS_TITLE;
                    $newsData[$i]['news_description'] = $resultNews[$i]->NEWS_DESCRIPTION;
                    $newsData[$i]['date'] = $resultNews[$i]->DATE;

                    $photo['small_url'] = $resultNews[$i]->PHOTO_SMALL;
                    $photo['medium_url'] = $resultNews[$i]->PHOTO_MEDIUM;
                    $photo['large_url'] = $resultNews[$i]->PHOTO_LARGE;
                    $newsData[$i]['news_photo'] = $photo;
                }
                $data['news'] = $newsData;
            }
            else
            {
                $data['news'] = [];
            }

        }
        else
        {
            $data['status'] = true;
            $data['message'] = 'Get news success!';
        }

        return $data;
    }
}
