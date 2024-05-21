@extends('admin.layouts.app')

@section('css')
<style>
    #btn-add, #btn-add-multiple {
        margin: 0px -10px 20px 15px;
    }
    .btn-group button {
        margin: 0px 4px;
    }
    .icon-big {
        font-size: 2.1em;
    }
    #pwInfo {
        font-style: italic;
        font-size: 12.5px;
    }
    .select2-container {
        width: 100% !important;
        padding: 0;
    }
    .swal-text {
        text-align: center !important;
    }
</style>
@endsection

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Customer</h4>
                </div>
                <div class="card-body">
                    <button type="button" id="btn-add" class="btn btn-primary btn-md">
                        Tambah Customer
                    </button>
                    <div class="table-responsive">
                        <table id="table-data" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th width="40px">No</th>
                                    <th>Nama Customer</th>
                                    <th>email</th>
                                    <th>No Hp</th>
                                    <th>Alamat</th>
                                    <th width="80px">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalData" role="dialog" aria-labelledby="modalDataLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            <form id="form-data" method="post" action="{{ route('admin.master.customer.store') }}">
              @csrf
              <input type="hidden" name="key" class="form-control" id="key-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDataLabel">Modal title</h5>
                </div>
                <div class="modal-body" id="modal-body">
                    <div class="form-group">
                        <label for="name-form">Nama Customer</label>
                        <input type="text" name="name" class="form-control" id="name-form" placeholder="Masukan Nama Customer" required/>
                    </div>
                    <div class="form-group">
                        <label for="username-form">Username</label>
                        <input type="text" name="username" class="form-control" id="username-form" placeholder="Masukan Username" required/>
                    </div>
                    <div class="form-group">
                        <label for="email-form">Email</label>
                        <input type="email" name="email" class="form-control" id="email-form" placeholder="Masukan Email" required/>
                    </div>
                    <div class="form-group">
                        <label for="alamat-form">Alamat</label>
                        <textArea name="alamat" class="form-control" id="alamat-form"></textArea>
                    </div>
                    <div class="form-group">
                        <label for="kode_pos-form">Kode Pos</label>
                        <input type="kode_pos" name="kode_pos" class="form-control" id="kode_pos-form" placeholder="Masukan Kode Pos" required/>
                    </div>
                    <div class="form-group">
                        <label for="kelurahan-form">Kelurahan</label>
                        <input type="kelurahan" name="kelurahan" class="form-control" id="kelurahan-form" placeholder="Masukan Kelurahan" required/>
                    </div>
                    <div class="form-group">
                        <label for="kecamatan-form">Kecamatan</label>
                        <input type="kecamatan" name="kecamatan" class="form-control" id="kecamatan-form" placeholder="Masukan Kecamatan" required/>
                    </div>
                    <div class="form-group">
                        <label for="kota-form">Kota</label>
                        <input type="kota" name="kota" class="form-control" id="kota-form" placeholder="Masukan Kota" required/>
                    </div>
                    <div class="form-group">
                        <label for="provinsi-form">Provinsi</label>
                        <input type="provinsi" name="provinsi" class="form-control" id="provinsi-form" placeholder="Masukan Provinsi" required/>
                    </div>
                    <div class="form-group">
                        <label for="no_hp-form">No Hp</label>
                        <input type="text" name="no_hp" class="form-control" id="no_hp-form" placeholder="Masukan No Hp" required/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-md" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-md">Simpan</button>
                </div>
            </form>
		</div>
	</div>
</div>
@endsection

@section('js')
<script>
    var dt;
    $(document).ready(function() {
 
        dt = $("#table-data").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.master.customer.scopeData') }}",
                type: "post"
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", searchable: "false", orderable: "false" },
                { data: "name", name: "name" },
                { data: "email", name: "email" },
                { data: "no_hp", name: "no_hp" },
                { data: "alamat", name: "alamat" },
                { data: "action", name: "action", searchable: "false", orderable: "false" }
            ],
            order: [[ 1, "asc" ]],
        });

        $("#btn-add").on("click",function(){
            $("#modalDataLabel").text("Tambah Data Customer");
            $("#modalData").modal("show");
        });

        $("body").on("click",".btn-edit",function(){
            $("#modalDataLabel").text("Ubah Data Customer");
            formLoading("#form-data","#modal-body",true);
            let key = $(this).data("key");
            $.ajax({
                url: "{{ route('admin.master.customer.detail') }}",
                type: "POST",
                data: {key:key},
                success:function(res){
                    $("#key-form").val(key);
                    $.each(res.data,function(k,v){
                        console.log(res.data);
                        if(k == 'id_gudang' && v !== '-'){
                            $gudangForm.append(`<option value="${v}" selected="selected">${res.data.gudang.nama_gudang}</option>`);
                        }
                        if(k == 'id_supplier' && v !== '-'){
                            $supplierForm.append(`<option value="${v}" selected="selected">${res.data.supplier.kode_supplier} - ${res.data.supplier.nama_supplier}</option>`);
                        }
                        $(`#${k}-form`).val(v).trigger("change");
                    });
                },
                error:function(err, status, message){
                    response = err.responseJSON;
                    message = (typeof response != "undefined") ? response.message : message;
                    notif("danger","fas fa-exclamation","Notifikasi Error",message,"error");
                },
                complete:function(){
                    formLoading("#form-data","#modal-body",false);
                }
            });
            $("#modalData").modal("show");
        });

        $("body").on("click",".btn-delete",function(){
            let key = $(this).data("key");
            swal({
                title: "Apakah anda yakin?",
                text: "Data yang dihapus tidak akan bisa dikembalikan!",
                icon: "warning",
                buttons:{
                    cancel: {
                        visible: true,
                        text : 'Batal',
                        className: 'btn btn-danger'
                    },
                    confirm: {
                        text : 'Yakin',
                        className : 'btn btn-primary'
                    }
                }
            }).then((willDelete) => {
                if (willDelete) {
                    notifLoading("Jangan tinggalkan halaman ini sampai proses penghapusan selesai !");
                    $.ajax({
                        url: "{{ route('admin.master.customer.destroy') }}",
                        type: "POST",
                        data: {key:key},
                        success:function(res){
                            notif("success","fas fa-check","Notifikasi Progress",res.message,"done");
                            dt.ajax.reload(null, false);
                        },
                        error:function(err, status, message){
                            response = err.responseJSON;
                            message = (typeof response != "undefined") ? response.message : message;
                            notif("danger","fas fa-exclamation","Notifikasi Error",message,"error");
                        },
                        complete:function(){
                            setTimeout(() => {
                                loadNotif.close();
                            }, 1000);
                        }
                    });
                }
            });
        });

        $("#modalData").on("hidden.bs.modal",function(){
            $("#password-form").prop("required",true);
            $("#pwInfo").addClass("hidden");
            if($("#key-form").val()) $("#form-data .form-control").val("");
        });

        $("#form-data").ajaxForm({
            beforeSend:function(){
                formLoading("#form-data","#modal-body",true,true);
            },
            success:function(res){
                dt.ajax.reload(null, false);
                notif("success","fas fa-check","Notifikasi Progress",res.message,"done");
                $("#form-data .form-control").val("")
                $("#modalData").modal("hide");
            },
            error:function(err, status, message){
                response = err.responseJSON;
                title = "Notifikasi Error";
                message = (typeof response != "undefined") ? response.message : message;
                if(message == "Error validation"){
                    title = "Error Validasi";
                    $.each(response.data, function(k,v){
                        message = v[0];
                        return false;
                    });
                }
                notif("danger","fas fa-exclamation",title,message,"error");
            },
            complete:function(){
                formLoading("#form-data","#modal-body",false);
            }
        });
    });
</script>
@endsection