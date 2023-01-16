<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\ElearningChallenge;
use App\Models\EventDetail;
use App\Models\Kuliah;
use App\Models\Periode;
use App\Models\Redeem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /*
        Berisi fungsi untuk: mendapatkan data profile pengguna dan refresh poin amtarewards.
        API dipanggil di proyek mobile: RestClient.cs
            View: MainAct.xaml
            View Model: MainViewModel.cs
    */
    public function __construct()
    {
        ini_set('memory_limit', '2048M'); //Menambah memory limit untuk menjalankan refresh kuliah log
        ini_set('max_execution_time', 72000); // 20 jam. Max waktu untuk menjalankan method dari file ini. 
                                            //Dulu untuk refresh log kuliah pada tahun tersebut semua pengguna (bukan hanya data kemarin saja)
        $this->amtanesia = new Amtanesia();
        $this->elearning = new ElearningChallenge();
        $this->kuliah = new Kuliah();
        $this->periode = new Periode();
        $this->user = new User();
        $this->event = new EventDetail();
        $this->redeem = new Redeem();
    }

    //GET STUDENT PROFILE
    public function getProfile(Request $request)
    {
        /*
		* Fungsi: Mendapatkan data profile (ID, nama, fakultas, prodi, foto, poin) mahasiswa . 
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL untuk autentikasi pengguna
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
         *             - student_profile       -> data profile mahasiswa
		 */

        $api_key = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($api_key);
        if ($resultApiKey['status']) {
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']); //dari model user. Untuk mengambil data mahasiswa.

            $profile['student_id'] = $resultProfile->ID_USERS;
            $profile['student_name'] = $resultProfile->NAME;
            $profile['student_faculty'] = $resultProfile->FAKULTAS;
            $profile['student_prodi'] = $resultProfile->PRODI;
            $profile['student_photo'] = $resultProfile->PHOTO;;
            $profile['student_point'] = $resultProfile->POINTS;
            $profile['student_info'] = 'student info';
            $profile['type'] = $resultProfile->TYPE;
            $data['status'] = true;
            $data['message'] = 'Get profile success!';
            $data['student_profile'] = $profile;
        } else {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        echo json_encode($data);
    }

    //REFRESH KULIAH LOG POINTS CRON JOB
    public function generateSaveLogKuliahAll()

    /** 
     * Fungsi: Memperbaharui log kuliah pada tabel elearning_history yang sudah sesuai dengan elearning challenge untuk SEMUA pengguna. 
     *          Kemudian, dihitung poinnya berdaasarkan log kuliah tersebut.
     * Dipanggil di Jobs/RefreshKuliahLog.php dan dijalankan menggunakan Cron Job.
     */
    {
        $users = User::all(); //Mendapatkan seluruh data pengguna
        $activePeriode = $this->periode->getActivePeriode(); //Mendapatkan ID periode aktif

        foreach ($users as $resultProfile) { //Melakukan perulangan untuk tiap pengguna
            $student = $this->kuliah->getUser($resultProfile->ID_USERS); //Mendapatkan data pengugna berdasarkan ID User
            if ($student != null) {
                $point = 0;
                $point_new_log = 0;
                $periode = $this->periode->getActiveYearPeriode();//dari Model Periode. Untuk mengambil data periode aktif.

                $periode_y_start = $periode->YEAR_START; //tanggal mulai periode
                $periode_y_finish = $periode->YEAR_FINISH; //tanggal selesai periode

                $eLearningChall = $this->elearning->getAllElearningChallenge($periode->ID_PERIOD, $resultProfile->TYPE);    //Mendapatkan seluruh elearning challenge periode sekarang
                $kuliahLog = $this->kuliah->getKuliahLogByDate($student->id, $periode_y_start, $periode_y_finish); //Mendapatkan log kuliah 1 hari sebelumnya (kemarin) milik pengguna tertentu dari database situs kuliah .

                $user_elearn_histories = $this->elearning->getAllElearningHistoryByUserId($resultProfile->ID_USERS); //Mendapatkan semua data elearning history pengguna tertentu
                if (count($eLearningChall) > 0) {
                    //SYNC LOG KULIAH MODEL LAMA (OLD. Biasanya sudah tidak dipakai)
                    if (count($kuliahLog) > 0) {
                        // sync
                        for ($i = 0; $i < count($kuliahLog); $i++) {

                            for ($j = 0; $j < count($eLearningChall); $j++) {
                                // get/filter log module action that match with elearning challenge
                                if ($kuliahLog[$i]->module == $eLearningChall[$j]->MODULE && $kuliahLog[$i]->action == $eLearningChall[$j]->ACTION) {
                                    //Mengecek apakah pengguna sudah mempunyai elearhing history dengan ID elearning history yang sama.
                                    if (is_null($this->elearning->getElearningHistoryByLogKuliahIdAndUserId($kuliahLog[$i]->id, $resultProfile->ID_USERS))) {
                                        // user not yet record this kuliah log item 
                                        if ($eLearningChall[$j]->MAX_COUNT != null) {
                                            //Pengecekan apakah log kuliah utk pengguna tertentu berjenis challenge tertentu sudah mencapai maksimum
                                            $isReachMax = $this->checkMaxLog(
                                                $eLearningChall[$j]->ID_ELEARNING_CHALLENGE,
                                                $resultProfile->ID_USERS,
                                                $kuliahLog[$i]->id,
                                                'old',
                                                $eLearningChall[$j]->MAX_COUNT
                                            );
                                            if (!$isReachMax) {
                                                //Apabila belum mencapai maksimum, buat buat data history
                                                $this->createHistoryContent($kuliahLog[$i], $resultProfile->ID_USERS, $eLearningChall[$j]->ID_ELEARNING_CHALLENGE);
                                                $point = $point + $eLearningChall[$j]->POINT;
                                            }
                                        } else {
                                            //Apabila challenge tidak memiliki batasan (maksimum), langsung buat data history
                                            $this->createHistoryContent($kuliahLog[$i], $resultProfile->ID_USERS, $eLearningChall[$j]->ID_ELEARNING_CHALLENGE);
                                            $point = $point + $eLearningChall[$j]->POINT;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //SYNC LOG KULIAH MODEL BARU (NEW) (Biasanya menggunakan yang ini)
                    $point_new_log = $this->syncElearningLogPoint($resultProfile->ID_USERS, $student->id, $periode_y_start, $periode_y_finish, $eLearningChall, 'All');
                    $point = $point + $point_new_log;
                    $this->user->addPointUser($point, $resultProfile->ID_USERS); //menambah poin baru dengan poin yang ada di tabel

                }
            }
        }
        return "Success!";
    }

    //REFRESH KULIAH LOG POINTS
    public function generateSaveLogKuliah(Request $request)
    {
        /** 
         * Fungsi: Memperbaharui log kuliah pada tabel elearning_history yang sudah sesuai dengan elearning challenge untuk pengguna tertentu (tiap kali membuka aplikasi). 
         *          Kemudian, dihitung poinnya berdasarkan log kuliah tersebut.
         * Param: 
         *       - request:
         *             - api_key    -> API key yang ada di parameter URL untuk autentikasi pengguna
         * Return:
         *      - data -> Array:
         *             - status     -> Status API Key valid atau tidak
         *             - message    -> Pesan pemanggilam API
         *             - poin       -> Jumlah poin sekarang pengguna tertentu
         * 
         * Dipanggil di RestClient.cs pada proyek mobile dan dijalankan saat pengguna tertentu login.
         */
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

        if ($resultApiKey['status']) {
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']); //Mendapat ID User berdasarkan ID device

            $student = $this->kuliah->getUser($resultProfile->ID_USERS); //Menggunakan ID User untuk mendapat data user

            if ($student != null) {
                $point = 0;
                $point_new_log = 0;
                $periode = $this->periode->getActiveYearPeriode(); //Mendapat data periode aktif

                $periode_y_start = $periode->YEAR_START;
                $periode_y_finish = $periode->YEAR_FINISH;

                $eLearningChall = $this->elearning->getAllElearningChallenge($periode->ID_PERIOD, $resultProfile->TYPE); //Mendapat elearning challenge untuk periode sekarang
                $kuliahLog = $this->kuliah->getKuliahLogByPeriode($student->id, $periode_y_start, $periode_y_finish); //Mendapatkan log kuliah pengguna tertentu dari database situs kuliah.

                // Ada 2 tipe log kuliah: old dan new. Biasanya data log sudah new.
                if (count($eLearningChall) > 0) {

                    //SYNC LOG KULIAH MODEL LAMA (OLD)
                    if (count($kuliahLog) > 0) {
                        // sync
                        for ($i = 0; $i < count($kuliahLog); $i++) {

                            for ($j = 0; $j < count($eLearningChall); $j++) {
                                // get/filter log module action that match with elearning challenge
                                if ($kuliahLog[$i]->module == $eLearningChall[$j]->MODULE && $kuliahLog[$i]->action == $eLearningChall[$j]->ACTION) {
                                    // Check apakah user belum memiliki data log kuliah dengan ID tertentu
                                    if (count($this->elearning->getElearningHistoryByLogKuliahIdAndUserId($kuliahLog[$i]->id, $resultProfile->ID_USERS)) < 0) {
                                        if ($eLearningChall[$j]->MAX_COUNT != null) {
                                            //Check apakah data history untuk challenge tertentu sudah mencapai maksimum yang diperbolehkan
                                            $isReachMax = $this->checkMaxLog(
                                                $eLearningChall[$j]->ID_ELEARNING_CHALLENGE,
                                                $resultProfile->ID_USERS,
                                                $kuliahLog[$i]->id,
                                                'old',
                                                $eLearningChall[$j]->MAX_COUNT
                                            );
                                            if (!$isReachMax) {
                                                //Buat data history apabila belum mencapai maksimum
                                                $this->createHistoryContent($kuliahLog[$i], $resultProfile->ID_USERS, $eLearningChall[$j]->ID_ELEARNING_CHALLENGE);
                                                $point = $point + $eLearningChall[$j]->POINT;
                                            }
                                        } else {
                                            //Apabila challenge tidak memiliki batasan (maksimum), langsung buat data history
                                            $this->createHistoryContent($kuliahLog[$i], $resultProfile->ID_USERS, $eLearningChall[$j]->ID_ELEARNING_CHALLENGE);
                                            $point = $point + $eLearningChall[$j]->POINT;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //SYNC LOG KULIAH MODEL BARU (NEW)
                    $point_new_log = $this->syncElearningLogPoint($resultProfile->ID_USERS, $student->id, $periode_y_start, $periode_y_finish, $eLearningChall, 'Specific');
                    return $point_new_log;

                    $point = $point + $point_new_log;
                    $this->user->addPointUser($point, $resultProfile->ID_USERS); //Menambah poin baru ke poin yang ada di tabel

                    $data['status'] = true;
                    $data['message'] = 'Refresh eLearning history success!';
                    $data['point'] = $point;
                } else {
                    $data['status'] = true;
                    $data['message'] = 'Refresh eLearning history success!';
                    $data['point'] = 0;
                }
            } else {
                $data['status'] = false;
                $data['message'] = 'Api Key is not valid!';
            }
        }

        return json_encode($data);
    }

    public function syncElearningLogPoint($userid, $userLogid, $periode_y_start, $periode_y_finish, $eLearningChall, $refreshType)
    {
        /** 
         * Fungsi: Memperbaharui log kuliah pada tabel elearning_history yang sudah sesuai dengan elearning challenge untuk pengguna tertentu. 
         *          Kemudian, dihitung poinnya berdasarkan log kuliah tersebut.
         * Param: 
         *       - userId -> ID User yang akan direfresh log kuliah dan poinnya
         *       - userLogid -> 
         *       - periode_y_start -> Tanggal periode aktif dimulai
         *       - periode_y_finish -> Tanggal periode aktif selesai  
         *       - eLearningChall -> Data elearning challenge
         * Return:
         *      - point -> jumlah poin dari log kuliah baru
         * 
         */

        $point = 0;
        //Mendapat data log kuliah periode sekarang dari situs kuliah
        if($refreshType == 'All') {
            //Apabila menjalankan refresh kuliah log all
            $kuliahLog  = $this->kuliah->getKuliahLogStandardYesterday($userLogid, $periode_y_start, $periode_y_finish);
        } else if($refreshType == 'Specific') {
            //Apabila menjalankan refresh kuliah log untuk pengguna spesifik 
            $kuliahLog  = $this->kuliah->getKuliahLogStandard($userLogid, $periode_y_start, $periode_y_finish);
        }
        //Mendapat elearning history yang bertipe new dari pengguna tertentu.
        //$user_elearn_histories     = $this->elearning->getAll_new_ElearningHistoryByUserId($userid);


        if (count($kuliahLog) > 0 && count($eLearningChall) > 0) {
            for ($i = 0; $i < count($kuliahLog); $i++) {
                for ($j = 0; $j < count($eLearningChall); $j++) {
                    if ($kuliahLog[$i]->eventname == $eLearningChall[$j]->EVENTNAME_CODE) {
                        //Check apakah sudah ada elearning history dengan ID yang sama pada user tertentu.
                        if (count($this->elearning->getAll_new_ElearningHistoryByUserIdAndLogKuliahId($kuliahLog[$i]->id, $userid)) < 0) {
                            //Apabila ada batasan maksimum untuk elearning challenge tertentu
                            if ($eLearningChall[$j]->MAX_COUNT != null) {
                                //Check apakah elearning history pengguna tertentu untuk challenge tertentu sudah mencapai maksimum
                                $isReachMax = $this->checkMaxLog(
                                    $eLearningChall[$j]->ID_ELEARNING_CHALLENGE,
                                    $userid,
                                    $kuliahLog[$i]->id,
                                    'new',
                                    $eLearningChall[$j]->MAX_COUNT
                                );
                                //Apabila belum mencapai maksimum
                                if (!$isReachMax) {
                                    $point = $point + $eLearningChall[$j]->POINT;
                                    //Menambah data history
                                    $history = $this->createHistoryContent_std_logstore($kuliahLog[$i], $userid, $eLearningChall[$j]['ID_ELEARNING_CHALLENGE']);
                                }
                            } else {
                                $point = $point + $eLearningChall[$j]->POINT;
                                //Menambah data history
                                $history = $this->createHistoryContent_std_logstore($kuliahLog[$i], $userid, $eLearningChall[$j]->ID_ELEARNING_CHALLENGE);
                            }
                        }
                    }
                }
            }
        }
        return $point;
    }

    public function createHistoryContent($kuliahLog, $idUser, $idElearningChallenge)
    {
        /** 
         * Fungsi: Menambah data history ke tabel elearning history untuk log history model OLD
         * Param: 
         *       - idUser -> ID User yang akan dibuat history-nya
         *       - kuliahLog -> data log kuliah
         *       - idELearningChall -> Data elearning challenge
         * Return:
         *      - point -> jumlah poin dari log kuliah baru
         * 
         */
        $course = $kuliahLog->course;
        $module = $kuliahLog->module;
        $action = $kuliahLog->action;
        $cmid = $kuliahLog->cmid;
        $url = $kuliahLog->url;
        $fetch_id = null;
        $ip_address = $kuliahLog->ip;
        $info = '';

        $fullname_course = $this->kuliah->getKuliahCourseFullname($course); //Mendapat nama lengkap dari sebuah mata kuliah

        if ($cmid == 0) {
            $part = parse_url($url);
            if (isset($part['query'])) {
                parse_str($part['query'], $query);
                if (isset($query['id'])) {
                    $fetch_id = $query['id'];
                }
            }
        } else {
            $fetch_id = $cmid;
        }

        // try to get detail info kuliah log by get info from correspond table with fetch id
        // if dont have fetch id, so no info will appear or just ''
        if ($fetch_id == null) {
        } else {
            $resultLogDisplay = $this->kuliah->getKuliahLogDisplay($module, $action); //Mendapat data standard log kuliah berdasarkan mpdule atau action tertentu
            if (count($resultLogDisplay) > 0) {
                $mtable = $resultLogDisplay->mtable;
                $field = $resultLogDisplay->field;
                $instanceId = $this->kuliah->getInstanceIdFromCourseModule($fetch_id); //Mendapat instance berdasarkan ID CMID/Fetch ID tertentu

                $info = '' . $fullname_course;

                if ($instanceId != null) {
                    $resultLogDetail = $this->kuliah->getKuliahLogDetail($mtable, $field, $instanceId->instance);

                    if (count($resultLogDetail) > 0) {
                        $info = '' . $fullname_course . ' - ' . $resultLogDetail->$field;
                    }
                }
            }
        }

        $kul_log_object_id = $this->getElearningLogObjectId('old', $kuliahLog['id']);

        //if(property_exists($info, 'fullname')) $info = $info->fullname; //ada yang pakai properti fullname, ada yang tidak.

        $newElearningHistory = array(
            'ID_USERS' => $idUser,
            'ID_ELEARNING_CHALLENGE' => $idElearningChallenge,
            'ID_LOG_KULIAH' => $kuliahLog->id,
            'INFO' => $info,
            'IP_ADDRESS' => $ip_address,
            'DATE_HISTORY' => gmdate("Y-m-d H:i:s", $kuliahLog['time']),
            'TYPE' => 'old',
            'KUL_LOG_OBJECT_ID ' => $kul_log_object_id
        );
        //Memasukkan data ke tabel elarning history
        $data = $this->elearning->insertElearningHistory($newElearningHistory);
    }


    public function checkMaxLog($challId, $userId, $idLogKuliah, $type, $maxCount)
    {

        /** 
         * Fungsi: Menmeriksa apakah elearning history challenge tertentu milik user tertentu sudah mencapai max
         *         Supaya tidak bisa mengeksploitasi pendapatan poin secara berulang-ulang dalam satu hari.
         * Param: 
         *       - challId -> ID elearning challenge
         *       - userId -> ID user yang akan diperisa
         *       - idLogKuliah -> ID log kuliah
         *       - type -> tipe log kuliah (old/new)
         *       - maxCount -> batasan maksimal dari challenge tertentu 
         * Return:
         *      - reachMax -> Boolean -> apakah mencapai maksinum atau tidak
         * 
         */

        $reachMax = false;
        $selectedElearningHistory = $this->elearning->getItemElearningHistoryByIdChall($challId, $userId); //mendapat ID elearning history berdasarkan challenge dan ID user
        $objectId = $this->getElearningLogObjectId($type, $idLogKuliah);

        if ($objectId == false) {
            // row with object id return null or row doesn't exist in database kuliah
            $reachMax = true;
        } else {
            if (count($selectedElearningHistory) > 0) {

                $i = 1;
                foreach ($selectedElearningHistory as $value) {

                    $matchLogObjectId = $this->getElearningLogObjectId($value->TYPE, $value->ID_LOG_KULIAH); //Mendapat object id

                    if (
                        $value->ID_USERS == $userId &&
                        $value->ID_ELEARNING_CHALLENGE == $challId &&
                        $matchLogObjectId == $objectId
                    ) {
                        //Apabila id user, id challenge, dan id object sama, maka sudah ada history utk challenge tersebut.
                        $i++;
                    }
                }   

                if ($i > $maxCount) { //apabila melebihi batasan
                    $reachMax = true;
                }
            }
        }
        return $reachMax;
    }

    public function getElearningLogObjectId($type, $logId)
    {
        /** 
         * Fungsi: Memeriksa apakah elearning history challenge tertentu milik user tertentu sudah mencapai max
         *         Supaya tidak bisa mengeksploitasi pendapatan poin secara berulang-ulang dalam satu hari.
         * Param: 
         *       - type -> tipe log kuliah (new/old)
         *       - logId -> ID log kuliah
         * Return:
         *      - objectId 
         * 
         */
        if ($type == 'old') { //TIPE LOG OLD
            $rowItemLogKuliah = $this->kuliah->getKuliahLogById($logId); //Mendapat data log kuliah dengan ID tertentu
            if (isset($rowItemLogKuliah->cmid)) { //apabila ada nilai CMID
                $objectId = $rowItemLogKuliah->cmid; 
            } else {
                $objectId = 'null';
            }
        } else { //TIPE LOG NEW
            $rowItemLogKuliah = $this->kuliah->getKuliahLogStandardById($logId); //Mendapat data log kuliah dengan ID tertentu
            // check if objecttable exist in kuliah log table ref
            if ($rowItemLogKuliah->objecttable == NULL) {
                $objectId = -2;
            } else {
                // check if objecttable exist in kuliah log table ref
                $refTable = $this->elearning->getKuliahLogTableRef($rowItemLogKuliah->objecttable);   /*Mendapat data table ref berdasarkan nama tabel */
                if ($refTable != NULL) {
                    //Mendapat object id
                    $objectId = $this->kuliah->getLogObjectId($rowItemLogKuliah->objectid, $rowItemLogKuliah->objecttable, $refTable->FIELD_OBJECT_ID);
                } else {
                    $objectId = $rowItemLogKuliah->objectid;
                }
            }
        }

        return $objectId;
    }


    public function createHistoryContent_std_logstore($kuliahLog, $idUser, $idElearningChallenge)
    {
        /** 
         * Fungsi: Menambah data history ke tabel elearning history untuk log history model NEW
         * Param: 
         *       - idUser -> ID User yang akan dibuat history-nya
         *       - kuliahLog -> data log kuliah
         *       - idELearningChall -> Data elearning challenge
         * Return:
         *      - point -> jumlah poin dari log kuliah baru
         * 
         */
        $objecttable = $kuliahLog->objecttable;
        $objecttid = $kuliahLog->objectid;
        $fullname_course = '';

        if ($kuliahLog->courseid != 0) {
            $fullname_course = $this->kuliah->getKuliahCourseFullname($kuliahLog->courseid); //Mendapat nama lengkap mata kuliah berdasarkan ID mata kuliah
        }
        $result_field = $fullname_course;

        if ($objecttable !== null && $objecttid !== null) {
            $field = $this->kuliah->getFieldTable($objecttable);

            if ($field !== null) {
                $detailLog = $this->kuliah->getKuliahLogDetail($objecttable, $field, $objecttid);

                if (isset($field->field)) {
                    $param = $field->field;
                    if (!is_null($detailLog)) {
                        if (property_exists($detailLog, $param)) {
                            $result_field = $fullname_course->fullname . ' - ' . $detailLog->$param;
                        } else {
                            $result_field = $fullname_course->fullname;
                        }
                    } else {
                        $result_field = $fullname_course->fullname;
                    }
                } 
            }
        }

        $kul_log_object_id = $this->getElearningLogObjectId("new", $kuliahLog->id); //get object id
    
        if ($kul_log_object_id == false) {
            $kul_log_object_id = -1;
        }

        if (property_exists($result_field, 'fullname')) $result_field = $result_field->fullname; //ada yang pakai properti fullname, ada yang tidak.

        $newElearningHistory = array(
            'ID_USERS' => $idUser,
            'ID_ELEARNING_CHALLENGE' => $idElearningChallenge,
            'ID_LOG_KULIAH' => $kuliahLog->id,
            'INFO' => $result_field,
            'IP_ADDRESS' => $kuliahLog->ip,
            'DATE_HISTORY' => gmdate("Y-m-d H:i:s", $kuliahLog->timecreated),
            'TYPE' => 'new',
            'KUL_LOG_OBJECT_ID' => $kul_log_object_id
        );
        
        $data = $this->elearning->insertElearningHistory($newElearningHistory); //Membuat data history
    }

    //REFRESH POINT

    public function refreshPointBase($resultProfile, $activePeriode, $start, $finish)
    {
        /** 
         * Fungsi: Menambah data history ke tabel elearning history untuk log history model NEW
         * Param: 
         *       - resultProfile -> data info user
         *       - activePeriode -> ID periode aktif
         *       - start -> tanggal periode aktif dimulai
         *       - finish -> tanggal periode aktif selesai
         * Return:
         *      - totalPoint -> jumlah poin dari log kuliah baru
         * 
         */

        //Mendapatkan jumlah poin dari E-learning History berdasarkan ID User, ID Periode dan tanggal tertentu.
        $reachedPointLogElearningHistory = $this->elearning->getReachedPointElearningHistory($resultProfile->ID_USERS, $activePeriode, $start, $finish);
        //Mendapat jumlah poin dari history keikutsertaan event berdasarkan user dan periode tertentu
        $reachedPointChallengeHistory = $this->event->getReachedPointChallenge($resultProfile->ID_USERS, $activePeriode,  $start, $finish);
        //Mendapat jumlah poin yang sudah digunakan/diredeem
        $consumedPointRedeem = $this->redeem->getConsumedPointRedeem($resultProfile->ID_USERS, $activePeriode, $start, $finish);

        $totalPoint = $reachedPointChallengeHistory + $reachedPointLogElearningHistory - $consumedPointRedeem;

        //Memperbaharui poin untuk user tertentu
        $this->user->updatePointUser($totalPoint, $resultProfile->ID_USERS);

        return $totalPoint;
    }

    public function refreshPoint(Request $request)
    {
        /*
		* Fungsi: Refresh poin saat pengguna membuka aplikasi amtarewards . 
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL untuk autentikasi pengguna
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
         *             - point       -> poin yang dimiliki
		 */
        
        $apiKey = $request->get('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

        if ($resultApiKey['status']) {
            $data['status'] = true;
            $data['message'] = 'Refresh point success!';

            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
            $activePeriode = $this->periode->getActivePeriode();

            $periode = $this->periode->getActiveYearPeriode();

            $start = $periode->YEAR_START;
            $finish = $periode->YEAR_FINISH;

            $totalPoint = $this->refreshPointBase($resultProfile, $activePeriode, $start, $finish); //dari controller ini. Untuk merefresh poin amtarewards per user.
            $data['point'] = $totalPoint;
        } else {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        echo json_encode($data);
    }

    public function refreshPointAll()
    {

        /*
		* Fungsi: Merefresh poin untuk semua user. 
		 */

        $users = User::all();

        $activePeriode = $this->periode->getActivePeriode(); //dari Model Periode. Untuk mendapat ID periode aktif.
        $periode = $this->periode->getActiveYearPeriode(); //dari Model Periode. Untuk mendapat satu baris data periode.
        $start = $periode->YEAR_START;
        $finish = $periode->YEAR_FINISH;

        foreach ($users as $resultProfile) {
            $this->refreshPointBase($resultProfile, $activePeriode, $start, $finish); //dari controller ini. Untuk merefresh poin amtarewards per user.
        }

        return "Success!";
    }
}
