<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterGudang;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use Validator;

class GudangController extends Controller
{
    /**
     * Return sap gudang settings view
     */
    public function index()
    {
        return view('admin.master.gudang.index');
    }

    /**
     * Return sap gudang data for datatables
     */
    public function scopeData(Request $req)
    {
        $data = MasterGudang::select('*');
        return DataTables::of($data)
                ->addIndexColumn()
                ->removeColumn('id')
                ->addColumn('action', function($val) {
                    $key = encrypt("gudang".$val->id);
                    return '<div class="btn-group">'.
                                '<button class="btn btn-info btn-sm btn-edit" data-key="'.$key.'" title="Lihat Barang"><i class="fas fa-box"></i></button>'.
                                '<button class="btn btn-warning btn-sm btn-edit" data-key="'.$key.'" title="Ubah Data"><i class="fas fa-pen"></i></button>'.
                                '<button class="btn btn-danger btn-sm btn-delete" data-key="'.$key.'" title="Hapus Data"><i class="fas fa-trash-alt"></i></button>'.
                            '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    /**
     * Return sap gudang settings detail
     */
    public function detail(Request $req)
    {
        try {
            $key = str_replace("gudang", "", decrypt($req->key));
            $data = MasterGudang::select('*')->whereId($key)->firstOrFail();
            return $this->sendResponse($data, "Berhasil mengambil data.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Data tidak dapat ditemukan.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin.");
        }
    }


    /**
     * Store create or update sap gudang settings
     */
    public function store(Request $req)
    {
        $pwRules = 'nullable';
      
        $validator = Validator::make($req->input(), [
            'key' => 'nullable|string',
            'nama_gudang' => 'required|string',
            'alamat' => 'required|string',
            'kontak' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            if(empty($req->key)){
                // Create Data
                $data = MasterGudang::create([
                    'nama_gudang' => $req->nama_gudang,
                    'alamat' => $req->alamat,
                    'kontak' => $req->kontak,
                ]);
                // Save Log
            } else {
                // Validation
                $key = str_replace("gudang", "", decrypt($req->key));
                $data = MasterGudang::findOrFail($key);
                // Update Data
                $data->update([
                    'nama_gudang' => $req->nama_gudang,
                    'alamat' => $req->alamat,
                    'kontak' => $req->kontak,
                ]);
            }
            return $this->sendResponse(null, "Berhasil memproses data.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Data tidak dapat ditemukan.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses penyimpanan data, silahkan hubungi admin");
        }
    }

    /**
     * Delete sap gudang data from db
     */
    public function destroy(Request $req)
    {
        try {
            // Validation
            $key = str_replace("gudang", "", decrypt($req->key));
            $data = MasterGudang::findOrFail($key);
            // Delete Process
            $data->delete();
            return $this->sendResponse(null, "Berhasil menghapus data.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Data tidak dapat ditemukan.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses penghapusan data, silahkan hubungi admin");
        }
    }

}
