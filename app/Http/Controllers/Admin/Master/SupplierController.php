<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterSupplier;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use Validator;

class SupplierController extends Controller
{
    /**
     * Return sap supplier settings view
     */
    public function index()
    {
        return view('admin.master.supplier.index');
    }

    /**
     * Return sap supplier data for datatables
     */
    public function scopeData(Request $req)
    {
        $data = MasterSupplier::select('*');
        return DataTables::of($data)
                ->addIndexColumn()
                ->removeColumn('id')
                ->addColumn('action', function($val) {
                    $key = encrypt("supplier".$val->id);
                    return '<div class="btn-group">'.
                                '<button class="btn btn-warning btn-sm btn-edit" data-key="'.$key.'" title="Ubah Data"><i class="fas fa-pen"></i></button>'.
                                '<button class="btn btn-danger btn-sm btn-delete" data-key="'.$key.'" title="Hapus Data"><i class="fas fa-trash-alt"></i></button>'.
                            '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    /**
     * Return sap supplier settings detail
     */
    public function detail(Request $req)
    {
        try {
            $key = str_replace("supplier", "", decrypt($req->key));
            $data = MasterSupplier::select('*')->whereId($key)->firstOrFail();
            return $this->sendResponse($data, "Berhasil mengambil data.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Data tidak dapat ditemukan.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin.");
        }
    }


    /**
     * Store create or update sap supplier settings
     */
    public function store(Request $req)
    {
        $pwRules = 'nullable';
      
        $validator = Validator::make($req->input(), [
            'key' => 'nullable|string',
            'kode_supplier' => 'required|string',
            'nama_supplier' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            if(empty($req->key)){
                // Create Data
                $data = MasterSupplier::create([
                    'kode_supplier' => $req->kode_supplier,
                    'nama_supplier' => $req->nama_supplier,
                    'keterangan' => $req->keterangan,
                ]);
                // Save Log
            } else {
                // Validation
                $key = str_replace("supplier", "", decrypt($req->key));
                $data = MasterSupplier::findOrFail($key);
                // Update Data
                $data->update([
                    'kode_supplier' => $req->kode_supplier,
                    'nama_supplier' => $req->nama_supplier,
                    'keterangan' => $req->keterangan,
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
     * Delete sap supplier data from db
     */
    public function destroy(Request $req)
    {
        try {
            // Validation
            $key = str_replace("supplier", "", decrypt($req->key));
            $data = MasterSupplier::findOrFail($key);
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
