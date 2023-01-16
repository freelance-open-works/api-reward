<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Periode;
use App\Models\Redeem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exports\PackageExport;
use Carbon\Carbon;
use Error;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    /*
        Berisi fungsi untuk mengelola formulir data paket.
        API dipanggil di views PackageManager.vue
    */

    public function __construct()
    {
        $this->redeem = new Redeem();
        $this->user = new User();
        $this->periode = new Periode();
        $this->package = new Package();
    }

    public function index($periodeId)
    {
        $data = $this->redeem->getCountAllRedeem($periodeId); //dari Model Redeem. Data redeem diolah menjadi jumlah redeem total, not started, pending, dan finished.

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    public function indexForm()
    {
        /* Mendapat semua data formulir paket. */
        $data = Package::join('users AS u', 'package.ID_USERS', 'u.ID_USERS')
            ->select(
                'package.ID_PACKAGE',
                'u.ID_USERS',
                'u.NAME',
                'package.ALAMAT',
                'package.JAM',
                'package.TANGGAL',
                'package.PENERIMA',
                'package.KODE_KIRIM',
                'package.ISI_PAKET',
                'package.ISI_PAKET_NAME',
                'package.ID_PERIOD'
            )
            ->get();

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }


    public function getProductListUser($userId, $periodeId)
    {
        /*
            Fungsi: Menampilkan produk redeem oleh user tertentu di periode tertentu.
            Param: 
                - userId          -> ID User yang akan dicari
                - periodeId       -> ID Periode yang akan dicari
            Return: 
                - message    -> String -> Pesan pemanggilan API
                - data       -> Object -> Data produk yang di-redeem oleh user tertentu
                - user       -> Object -> Data informasi user dari ID User tersebut
        */
        $data = $this->redeem->getProductListUser($userId, $periodeId); //dari Model redeem. 
       
        $user = $this->user->getUsersById($userId); //dari Model User. Menampilkan data user untuk menampilkan nama user di field nama.

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
                'user' => $user,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    public function getPackageName($id)
    {
        /*
            Fungsi: Mendapatkan nama paket yang awalnya berupa ID redeem.
            Param: 
                - id            -> ID Paket yang akan dicari nama-nama paketnya
            Return: String      -> String yang berisi nama-nama paket, dipisahkan dengan koma.
        */

        $data = $this->package->getPackageName($id);
        $string = "";

        //Menyambungkan array nama paket menjadi 1 kalimat string yang dipisahkan dengan koma.
        foreach ($data as $value) {
            if ($string == "") $string = $value->NAME_CATALOGUE;
            else $string = $string . ", " . $value->NAME_CATALOGUE;
        }
        return $string;
    }

    public function getOtherPackage($packageId)
    {
        /*
            Fungsi: Mendapatkan katalog/produk redeem user tertentu yang tidak dipilih pada ID paket (formulir) tertentu. Untuk men-disable checkbox apabila barang sudah on process di formulir lain.
            Param: 
                - packageId     -> ID Paket
            Return: 
                - message       -> String      -> Pesan pemanggilan API
                - data          -> Object      -> Data katalog/produk yang tidak dipilih pada formulir tersebut.
        */

        $data = $this->package->getOtherPackage($packageId);
       
        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve Other Package Success',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    public function changeRedeemStatus($data, $status)
    {
        /** 
         * Fungsi: mengganti status redeem sesuai dengan parameter.
         * Param:
         *      - data   -> Array   -> data ID redeem yang akan diganti statusnya
         *      - status -> String  -> status baru yang akan digunakan untuk mengganti status lama.
        */
        $paket = json_decode($data);
        foreach ($paket as $item) {
            Redeem::where('ID_REDEEM_LOG', $item)->update(['ID_REDEEM_STATUS' => $status]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ID_PERIOD' => 'required',
            'ID_USERS' => 'required',
            'ALAMAT' => 'required',
            'JAM' => 'required',
            'TANGGAL' => 'required',
            'PENERIMA' => 'required',
            'ISI_PAKET' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }
        $this->changeRedeemStatus($data['ISI_PAKET'], 3);

        $data = Package::create($data); //menambah data pada product baru

        //Update kolom ISI_PAKET_NAME yang berisi nama-nama paket
        $this->updatePackageName($data->ID_PACKAGE);

        return response([
            'message' => 'Add Package Form Success',
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = Package::find($id);

        if (is_null($data)) {
            return response([
                'message' => 'Package Form Not Found',
                'data' => null
            ], 404); //return message data product tidak ditemukan
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'ID_USERS' => 'required',
            'ALAMAT' => 'required',
            'JAM' => 'required',
            'TANGGAL' => 'required',
            'PENERIMA' => 'required',
            'ISI_PAKET' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $update = array(
            'ID_USERS' => $updateData['ID_USERS'],
            'ALAMAT' => $updateData['ALAMAT'],
            'JAM' => $updateData['JAM'],
            'TANGGAL' => $updateData['TANGGAL'],
            'PENERIMA' => $updateData['PENERIMA'],
            'ISI_PAKET' => $updateData['ISI_PAKET'],
        );

        //Barang yang di-unselect dan akan diubah lagi statusnya jadi 1 (pending)
        $this->changeRedeemStatus($updateData['DIFFERENCE'], 1);
        $timeupdate = DB::table('redeem_log')
            ->whereIn('ID_REDEEM_LOG',  json_decode($updateData['DIFFERENCE']))
            ->update(['REDEEM_FINISHED_TIME' => '0000-00-00 00:00:00']);

        if (array_key_exists('KODE_KIRIM', $updateData)) { //Apabila ada kode_kirim, status jadi finish
            $update['KODE_KIRIM'] = $updateData['KODE_KIRIM'];

            if ($update['KODE_KIRIM'] == "") {
                $timeupdate = DB::table('redeem_log')
                    ->whereIn('ID_REDEEM_LOG',  json_decode($updateData['ISI_PAKET']))
                    ->update(['REDEEM_FINISHED_TIME' => '0000-00-00 00:00:00']);
                $this->changeRedeemStatus($updateData['ISI_PAKET'], 3); //jadi on process
            } else {
                //Update tanggal redeem
                $timeupdate = DB::table('redeem_log')
                    ->whereIn('ID_REDEEM_LOG', json_decode($updateData['ISI_PAKET']))
                    ->update(['REDEEM_FINISHED_TIME' => Carbon::now(+7)->toDateTimeString()]);
                $this->changeRedeemStatus($updateData['ISI_PAKET'], 2); //jadi finished
            }
        } else {
            $this->changeRedeemStatus($updateData['ISI_PAKET'], 3);
        }

        if ($data->update($update)) {
            //Update kolom ISI_PAKET_NAME yang berisi nama-nama paket
            $this->updatePackageName($id);

            return response([
                'message' => 'Update Package Form Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update Package Form Failed',
            'data' => null
        ], 400);  //return message saat produk gagal diedit
    }

    public function updatePackageName($id)
    {
        $packageName = $this->getPackageName($id);

        $affected = DB::table('package')
            ->where('ID_PACKAGE', $id)
            ->update(['ISI_PAKET_NAME' => $packageName]); //Pakai cara update biasanya entah kenapa tidak bisa
        //error_log("affected " .$affected);

        if ($affected) {
            return response([
                'message' => 'Update Package Name Success',
            ], 200);
        } //return data yang telah diedit dalam bentuk json

        return response([
            'message' => 'Update Package Name Failed',
            'data' => null
        ], 400);  //return message saat produk gagal diedit
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Package::find($id); //mencari data berdsaar id
        $timeupdate = DB::table('redeem_log')
            ->whereIn('ID_REDEEM_LOG',  json_decode($data['ISI_PAKET']))
            ->update(['REDEEM_FINISHED_TIME' => '0000-00-00 00:00:00']);

        if (is_null($data)) {
            return response([
                'message' => 'Package Form Not Found',
                'data' => null
            ], 404); //return message data product tidak ditemukan
        }

        $this->changeRedeemStatus($data->ISI_PAKET, 1);

        if ($data->delete()) {
            return response([
                'message' => 'Delete Package Form Success',
                'data' => $data
            ], 200); //return message data product berhasil dihapus
        }

        return response([
            'message' => 'Delete Package Form Failed',
            'data' => null
        ], 400); //return message data product gagal dihapus
    }

    //TODO: Mau coba2 excel dari package ini. Kalo engga, hapus.
    public function exportExcel()
    {
        // return "Test";
        return Excel::download(new PackageExport(), 'package.xlsx');
    }

    //Untuk mendapat data formulir berdasarkan periode. Digunakan di bagian export Excel berdasarkan periode.
    public function getPackageFormBasedOnPeriode($periode)
    {
        return $this->package->getPackageFormBasedOnPeriode($periode);
    }
}
