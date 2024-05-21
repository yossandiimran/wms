@extends('admin.layouts.app')

@section('css')
<style>
    #btn-add {
        margin: 0px 0px 20px 15px;
    }
    .btn-group button {
        margin: 0px 4px;
    }
    .icon-big {
        font-size: 2.1em;
    }
</style>
@endsection

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Master Gudang</h4>
                </div>
                <div class="card-body">
                    <button type="button" id="btn-add" class="btn btn-primary btn-md">
                        Tambah Data
                    </button>
                    <div class="table-responsive">
                        <table id="table-data" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th width="40px">No</th>
                                    <th>Nama Gudang</th>
                                    <th>Alamat</th>
                                    <th>Kontak</th>
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

<div class="modal fade" id="modalData" tabindex="-1" role="dialog" aria-labelledby="modalDataLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            <form id="form-data" method="post" action="{{ route('admin.master.gudang.store') }}">
              @csrf
              <input type="hidden" name="key" class="form-control" id="key-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDataLabel">Modal title</h5>
                </div>
                <div class="modal-body" id="modal-body">
                    <div class="form-group">
                        <label for="nama_gudang-form">Gudang</label>
                        <input type="text" name="nama_gudang" class="form-control" id="nama_gudang-form" placeholder="Masukan Nama Gudang" required/>
                    </div>
                    <div class="form-group">
                        <label for="alamat-form">Alamat</label>
                        <input type="text" name="alamat" class="form-control" id="alamat-form" placeholder="Masukan Alamat Gudang" required/>
                    </div>
                    <div class="form-group">
                        <label for="kontak-form">Kontak</label>
                        <input type="text" name="kontak" class="form-control" id="kontak-form" placeholder="Masukan Kontak" required/>
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
                url: "{{ route('admin.master.gudang.scopeData') }}",
                type: "post"
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", searchable: "false", orderable: "false" },
                { data: "nama_gudang", name: "nama_gudang" },
                { data: "alamat", name: "alamat" },
                { data: "kontak", name: "kontak" },
                { data: "action", name: "action", searchable: "false", orderable: "false" }
            ],
            order: [[ 1, "asc" ]],
        });

        $("#btn-add").on("click",function(){
            $("#modalDataLabel").text("Tambah Data Gudang");
            $("#modalData").modal("show");
        });

        $("body").on("click",".btn-edit",function(){
            $("#modalDataLabel").text("Ubah Data Gudang");
            $("#password-form").prop("required",false);
            $("#pwInfo").removeClass("hidden");
            formLoading("#form-data","#modal-body",true);
            let key = $(this).data("key");
            $.ajax({
                url: "{{ route('admin.master.gudang.detail') }}",
                type: "POST",
                data: {key:key},
                success:function(res){
                    $("#key-form").val(key);
                    $.each(res.data,function(k,v){
                        $(`#${k}-form`).val(v);
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
                        url: "{{ route('admin.master.gudang.destroy') }}",
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