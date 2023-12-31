<script>
    var save_method, table1, table2;
    $(document).ready(function () {
        handle_datatable_produk();
        handle_datatable_bisa_dijual();
        handle_validate();
    });

    function handle_datatable_produk()
    { 
        table1 = $("#table-produk-api").DataTable({
            processing: true,
            serverSide: true,
            order: [],

            ajax: {
                url: base_url + "produk-api/get_data_produk",
                type: "POST",
            },

            columnDefs: [
                {
                    targets: '_all',
                    orderable: false,
                },
            ],
        });
    }

    function handle_datatable_bisa_dijual()
    { 
        table2 = $("#table-produk-bisa-dijual").DataTable({
            processing: true,
            serverSide: true,
            order: [],

            ajax: {
                url: base_url + "produk-api/get_data_produk_bisa_dijual",
                type: "POST",
            },

            columnDefs: [
                {
                    targets: '_all',
                    orderable: false,
                },
            ],
        });
    }

    function add_produk()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Form Tambah Produk'); 
    }

    function save_produk()
    {
        $('#btnSave').text('saving...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        
        let url;
        let pesan_sukses;
        let pesan_gagal;
        if(save_method == 'add')
        {
            url = base_url + "produk-api/tambah-produk";
            pesan_gagal = 'Data Gagal Disimpan! ';
        }else
        {
            url = base_url + "produk-api/edit-produk";
            pesan_gagal = 'Data Gagal Diupdate! ';
        }

        $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data)
            {
                if(data.status == true) 
                {
                    $('#modal_form').modal('hide');
                    alert_sukses(data.message);
                    reload_table();
                }else
                {
                    for (let i = 0; i < data.inputerror.length; i++) 
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSave').html('<i class="fa fa-check"></i> Save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            },
            error: function (request)
            {
                alert_gagal(pesan_gagal + request.status + ' ' + request.statusText);
                $('#btnSave').html('<i class="fa fa-check"></i> Save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            }
        });
    }

    function edit_produk(id_produk)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        $.ajax({
            url : base_url + "produk-api/get_produk_by_id/"+id_produk,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="id_produk"]').val(data.id_produk);
                $('[name="nama_produk"]').val(data.nama_produk); 
                $('[name="harga"]').val(data.harga); 
                $('[name="kategori_id"]').val(data.kategori_id);
                $('[name="status_id"]').val(data.status_id);
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Form Edit Produk'); 
            },
            error: function (request)
            {
                alert_gagal('An error occurred during your request: ' +  request.status + ' ' + request.statusText);
            }
        });
    }

    function delete_produk(id_produk)
    {
        swal({
            title: "Anda yakin menghapus data ini ?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if(willDelete)
            {
                $.ajax({
                    url : base_url + "produk-api/hapus-produk/"+id_produk,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data)
                    {
                        if(data.status == true)
                        {
                            alert_sukses(data.message);
                            reload_table();
                        }else
                        {
                            alert_gagal(data.message);
                        }
                    },
                    error: function (request)
                    {
                        alert_gagal('Data Gagal Dihapus! ' +  request.status + ' ' + request.statusText);
                    }
                });
            }
        });
    }

    function reload_table()
    {
        table1.ajax.reload(); 
    }

    function reload_table_bisa_dijual()
    {
        table2.ajax.reload(); 
    }

    function handle_validate()
    {
        $("input").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
    }
    
    function alert_sukses(str)
    {
        setTimeout(function () { 
            swal({
                position: 'top-end',
                icon: 'success',
                title: str,
                timer: 1500
            });
        },2000); 
    }

    function alert_gagal(str)
    {
        setTimeout(function () { 
            swal({
                position: 'top-end',
                icon: 'error',
                title: str,
                timer: 5000
            });
        },2000); 
    }
</script>