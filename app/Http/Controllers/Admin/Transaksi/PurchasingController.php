<?php

namespace App\Http\Controllers\Admin\Transaksi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Masterbarang;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use DataTables;
use PDF;
use Validator;

class PurchasingController extends Controller
{
   
    public function index()
    {
        return view('admin.transaksi.purchasing.index');
    }

    public function create(){
        return view('admin.transaksi.purchasing.tambahPO');
    }

    public function scopeData(Request $req)
    {
        $data = Order::select('*');
        return DataTables::of($data)
                ->addIndexColumn()
                ->removeColumn('id')
                ->addColumn('customer', function($val) {
                    return $val->customer->name;
                })
                ->addColumn('created_at', function($val) {
                    return Carbon::parse($val->created_at)->format('d F Y');
                })
                ->addColumn('status_po', function($val) {
                    $key = encrypt("order".$val->id);
                    if($val->status_po == '1'){
                        return '<button class="btn btn-warning btn-sm btn-status" data-key="'.$key.'" type="button" title="Proses"><i class="fas fa-clock"></i>&nbsp;Proses</button>';
                    }else if($val->status_po == '2'){
                        return '<button class="btn btn-primary btn-sm btn-status" data-key="'.$key.'" type="button" title="Pengiriman"><i class="fas fa-truck"></i>&nbsp;Pengiriman</button>';
                    }else if($val->status_po == '3'){
                        return '<button class="btn btn-success btn-sm btn-status" data-key="'.$key.'" type="button" title="Selesai"><i class="fas fa-check"></i>&nbsp;Selesai</button>';
                    }else{
                        return '<button class="btn btn-danger btn-sm btn-status" data-key="'.$key.'" type="button" title="Dibatalkan / Gagal"><i class="fa fa-close"></i>&nbsp;Dibatalkan / Gagal</button>';
                    }
                })
                ->addColumn('action', function($val) {
                    $key = encrypt("order".$val->id);
                    return '<div class="btn-group">'.
                                '<a href="'.url('admin/transaksi/po/print', $key).'" class="btn btn-secondary btn-sm btn-edit" data-key="'.$key.'" title="Ubah Data"><i class="fas fa-print"></i> Cetak</a>'.
                                '<button class="btn btn-danger btn-sm btn-delete" data-key="'.$key.'" title="Hapus Data"><i class="fas fa-trash-alt"></i> Hapus</button>'.
                            '</div>';
                })
                ->rawColumns(['action', 'status_po'])
                ->make(true);
    }


    public function scopeList(Request $req){
        
        $data = Masterbarang::select('*');
        return DataTables::of($data)
            ->addIndexColumn()
            ->removeColumn('id')
            ->addColumn('gudang', function($val) {
                return $val->gudang->nama_gudang;
            })
            ->addColumn('supplier', function($val) {
                return $val->supplier->nama_supplier;
            })
            ->addColumn('action', function($val) {
                $key = encrypt("barang".$val->id);
                $jsonValue = json_encode($val);
                $base64Value = base64_encode($jsonValue);
                return '<div class="btn-group">'.
                            '<button class="btn btn-primary btn-md btn-primary" data-key="'.$key.'" title="Tambah Data" onclick=appendBarang("'.$base64Value.'") type="button">Pilih</button>'.
                        '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $req)
    {

        $validator = Validator::make($req->input(), [
            'key' => 'nullable|string',
            'no_po' => 'required|string',
            'id_customer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Error validation", $validator->errors());
        }

        try {
            if(empty($req->key)){
                // Create Data
                $data = Order::create([
                    'nomor_po' => $req->no_po,
                    'customer_id' => $req->id_customer,
                    'status_po' => 1,
                ]);

                $barang = $req->detail_barang;

                for($i=0;$i<count($barang);$i++){
                    $detail = OrderDetail::create([
                        'nomor_po' => $req->no_po,
                        'kode_barang' => $barang[$i]["kode_barang"],
                        'jumlah' => $barang[$i]["jumlah"],
                    ]);
                }
            } else {
                // Validation
                $key = str_replace("barang", "", decrypt($req->key));
                $data = Masterbarang::findOrFail($key);
                // Update Data
                $data->update([
                    'nama_barang' => $req->nama_barang,
                    'kode_barang' => $req->kode_barang,
                    'id_supplier' => $req->id_supplier,
                    'id_gudang' => $req->id_gudang,
                    'stok' => $req->stok,
                    'harga' => $req->harga,
                    'satuan' => $req->satuan,
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

    public function destroy(Request $req)
    {
        try {
            $key = str_replace("order", "", decrypt($req->key));
            $data = Order::findOrFail($key);
            $dataKey = Order::findOrFail($key)->first();
            OrderDetail::where('nomor_po', $dataKey->nomor_po)->delete();
            $data->delete();
            return $this->sendResponse(null, "Berhasil menghapus data.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Data tidak dapat ditemukan.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses penghapusan data, silahkan hubungi admin");
        }
    }

    // Cetak/Print PDF
    public function print($key)
    {
        $keys = str_replace("order", "", decrypt($key));
        $order = Order::select('*')->whereId($keys)->firstOrFail();
        // return view('admin.transaksi.purchasing.cetak', compact('order'));
        
        $pdf = PDF::loadView('admin.transaksi.purchasing.cetak', compact('order'));
        return $pdf->download('po.pdf');
    }

    public function ubahStatus(Request $req)
    {
        try {
            $key = str_replace("order", "", decrypt($req->key));
            $data = Order::select('*')->whereId($key)->update([
                'status_po' => $req->status_po,
            ]);
            return $this->sendResponse($data, "Berhasil mengambil data.");
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Data tidak dapat ditemukan.");
        } catch (\Throwable $err) {
            return $this->sendError("Kesalahan sistem saat proses pengambilan data, silahkan hubungi admin.");
        }
    }

}
