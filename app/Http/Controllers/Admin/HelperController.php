<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterBarang;
use App\Models\MasterSupplier;
use App\Models\MasterGudang;
use App\Models\User;
use Validator;

class HelperController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getSelectSupplier(Request $req){
        $validator = Validator::make($req->input(), [
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            $query = MasterSupplier::whereNotNull("id");
            if(!empty($req->search)){
                $search = $req->search;
                $query->where(function($q)use($search){
                    $q->where('nama_supplier','LIKE',"%$search%")->orWhere('kode_supplier','LIKE',"%$search%");
                });
            }
            $data = $query->orderBy("nama_supplier")->limit(250)->get();
            return $this->sendResponse($data, "Berhasil mendapatkan data.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin");
        }
    }

    public function getSelectGudang(Request $req){
        $validator = Validator::make($req->input(), [
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            $query = MasterGudang::whereNotNull("id");
            if(!empty($req->search)){
                $search = $req->search;
                $query->where(function($q)use($search){
                    $q->where('nama_gudang','LIKE',"%$search%")->orWhere('alamat','LIKE',"%$search%");
                });
            }
            $data = $query->orderBy("nama_gudang")->limit(250)->get();
            return $this->sendResponse($data, "Berhasil mendapatkan data.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin");
        }
    }

    public function getSelectBarang(Request $req){
        $validator = Validator::make($req->input(), [
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            $query = MasterBarang::whereNotNull("id");
            if(!empty($req->search)){
                $search = $req->search;
                $query->where(function($q)use($search){
                    $q->where('nama_barang','LIKE',"%$search%")->orWhere('kode_barang','LIKE',"%$search%");
                });
            }
            $data = $query->orderBy("nama_barang")->limit(250)->get();
            return $this->sendResponse($data, "Berhasil mendapatkan data.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin");
        }
    }

    public function getSelectCustomer(Request $req){
        $validator = Validator::make($req->input(), [
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            $query = User::whereNotNull("id")->where('group_user', '2');
            if(!empty($req->search)){
                $search = $req->search;
                $query->where(function($q)use($search){
                    $q->where('name','LIKE',"%$search%")->orWhere('username','LIKE',"%$search%");
                });
            }
            $data = $query->orderBy("name")->limit(250)->get();
            return $this->sendResponse($data, "Berhasil mendapatkan data.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin");
        }
    }

}
