<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use Validator;
use Hash;

class CustomerController extends Controller
{
    /**
     * Return sap customer settings view
     */
    public function index()
    {
        return view('admin.master.customer.index');
    }

    /**
     * Return sap customer data for datatables
     */
    public function scopeData(Request $req)
    {
        $data = User::select('*')->where('group_user', '2');
        return DataTables::of($data)
                ->addIndexColumn()
                ->removeColumn('id')
                ->addColumn('action', function($val) {
                    $key = encrypt("customer".$val->id);
                    return '<div class="btn-group">'.
                                '<button class="btn btn-warning btn-sm btn-edit" data-key="'.$key.'" title="Ubah Data"><i class="fas fa-pen"></i></button>'.
                                '<button class="btn btn-danger btn-sm btn-delete" data-key="'.$key.'" title="Hapus Data"><i class="fas fa-trash-alt"></i></button>'.
                            '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    /**
     * Return sap customer settings detail
     */
    public function detail(Request $req)
    {
        try {
            $key = str_replace("customer", "", decrypt($req->key));
            $data = User::select('*')->whereId($key)->firstOrFail();
            return $this->sendResponse($data, "Berhasil mengambil data.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Data tidak dapat ditemukan.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin.");
        }
    }


    /**
     * Store create or update sap customer settings
     */
    public function store(Request $req)
    {
        $pwRules = 'nullable';

      
        $validator = Validator::make($req->input(), [
            'key' => 'nullable|string',
            'name' => 'required|string',
            'username' => 'required|string',
            'alamat' => 'required|string',
            'email' => 'required|string',
            'no_hp' => 'required|string',
            'kode_pos' => 'required|string',
            'kota' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'provinsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            if(empty($req->key)){
                // Create Data
                $data = User::create([
                    'name' => $req->name,
                    'username' => $req->username,
                    'email' => $req->email,
                    'alamat' => $req->alamat,
                    'no_hp' => $req->no_hp,
                    'password' => Hash::make('admin123'),
                    'group_user' => 2,
                    'kota' => $req->kota,
                    'kode_pos' => $req->kode_pos,
                    'kelurahan' => $req->kelurahan,
                    'kecamatan' => $req->kecamatan,
                    'provinsi' => $req->provinsi,
                ]);
                // Save Log
            } else {
                // Validation
                $key = str_replace("customer", "", decrypt($req->key));
                $data = User::findOrFail($key);
                // Update Data
                $data->update([
                    'name' => $req->name,
                    'username' => $req->username,
                    'email' => $req->email,
                    'alamat' => $req->alamat,
                    'no_hp' => $req->no_hp,
                    'kota' => $req->kota,
                    'kode_pos' => $req->kode_pos,
                    'kelurahan' => $req->kelurahan,
                    'kecamatan' => $req->kecamatan,
                    'provinsi' => $req->provinsi,
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
     * Delete sap customer data from db
     */
    public function destroy(Request $req)
    {
        try {
            // Validation
            $key = str_replace("customer", "", decrypt($req->key));
            $data = User::findOrFail($key);
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
