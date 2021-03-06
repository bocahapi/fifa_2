<?php
/*
* Pengecekan Definisi UMS
*/
if(!defined('UMS')){
    header('location:./../404.html');
}

# Jika user belum login maka user dialihkan kehalaman login
if( !isset($_SESSION['login'])) {
    header('location:./../');
}
?>

<!-- Input Total Dana -->
<div class="content">
    <h3 class="title">Input Total Dana</h3>
    <form action="query/query.php" role="form">
        <table class="table-form">
            <tr>
                <td>User</td>
                <td>:</td>
                <td>
                    <input type="hidden" name="act" value="input-total">
                    <select name="user" id="" class="form-control input-sm">
                        <option value="">Pilih User</option>
                    <?php
                    $sql = "SELECT id_user,nama FROM `user` WHERE id_jabatan !='1' AND id_jabatan != '2' ";
                    $query = $mysqli->query($sql);
                    while($users = $query->fetch_assoc() ){
                        echo "<option value=\"{$users['id_user']}\">{$users['nama']}</option>";
                    }
                    $query->free();
                    ?>

                    </select>
                </td>
            </tr>
            <tr>
                <td>Jenis Dana</td>
                <td>:</td>
                <td>
                    <select name="dana" id="" class="form-control input-sm">
                        <option value="">Pilih Jenis Dana</option>
                        <?php
                           
                           $stmt = $mysqli->query("SELECT * FROM jenis_dana");
                            while($jenis_dana = $stmt->fetch_assoc()){
                                echo "<option value=\"{$jenis_dana['id_jenis']}\">{$jenis_dana['nama_jenis']}</option>"; 
                            }
                       $stmt->free();
                       ?>
                    
                    </select>
                </td>
            </tr>
        </table>
        <div class="s text-right">
            <div class="btn btn-primary btn-sm lihat">Lihat</div>
        </div>
    </form>

    <hr>
    
    <!-- form input dana total -->
    <div class="alert alert-danger hide"></div>
    <div class="input-total"></div>
    <!-- form input dana total -->
    
    <!-- View Dana -->
    <div class="view-dana">
        
    </div>
    <!-- View Dana -->
    
    <table class="table table-bordered">
        <tr>
            <th>Nomor</th>
            <th class="text-center">Tanggal</th>
            <th>User</th>
            <th>Jenis Dana</th>
            <th class="text-center">Aksi</th>
        </tr>

        <?php
        $no = 1;
        $sql  = "SELECT DISTINCT total_dana.id_user,total_dana.id_jenis,total_dana.tgl_total_dana,total_dana.tahun,user.nama,jenis_dana.nama_jenis FROM total_dana,user,jenis_dana WHERE total_dana.id_user = user.id_user AND total_dana.id_jenis = jenis_dana.id_jenis " ;
        $stmt = $mysqli->query($sql);
        while ($list_dana = $stmt->fetch_assoc()) {
            echo "<tr>"
                ."<td>{$no}</td>"
                ."<td class=\"text-center\">{$list_dana['tgl_total_dana']}/{$list_dana['tahun']}</td>"
                ."<td>{$list_dana['nama']}</td>"
                ."<td>{$list_dana['nama_jenis']}</td>"
                ."<td class=\"text-center aksi\">"
                    ."<a href=\"#\" data-id=\"{$list_dana['id_user']}-{$list_dana['id_jenis']}-{$list_dana['tahun']}\" data-act=\"lihat\" class=\"act\"><span class=\"glyphicon glyphicon-eye-open\"></span></a>"
                    ."<a href=\"#\" data-id=\"{$list_dana['id_user']}-{$list_dana['id_jenis']}-{$list_dana['tahun']}\" data-act=\"edit\" class=\"act\"><span class=\"glyphicon glyphicon-pencil\"></span></a>"
                    ."<a href=\"#\" data-id=\"{$list_dana['id_user']}-{$list_dana['id_jenis']}-{$list_dana['tahun']}\" data-act=\"delete\" class=\"act\"><span class=\"glyphicon glyphicon-trash\"></span></a>"
                ."</td>"
                ."</tr>";
            $no++;    
        }
        $stmt->free_result();
        ?>

    </table>

</div>

<script>
jQuery(function($){
    jQuery('.lihat').parents('form').keyup(function(event) {
        if( event.keyCode == 13 ){
        jQuery('.lihat').trigger('click');        
        }
    });

    // perintah untuk kolom aksi
    jQuery('.aksi a').click(function(event) {
        /*
            mengambil value dari attribut data-*
        */
        var act = $(this).data('act'); // ambil value dari "data-act"
        var id  = $(this).data('id'); // ambil value dari "data-id"

        /*
            jQuery Ajax 

            ref : http://hayageek.com/jquery-ajax-form-submit/
        */
        $.ajax({
            url:'query/query.php',
            type:'post',
            data:{act:act,id:id},
            success:function(result){


                if (act == 'delete') {
                
                    location.reload(true);
                
                }else if (act == 'edit') {

                    jQuery('.input-total').html(result);
                    /*
                        menambah tombol "Simpan" di Inputan Total Dana
                    */
                    jQuery('.simpan-btn').html('<div class="btn btn-primary btn-sm update ">Update</div>');

                    // Dan jika tombol "Simpan" di klik akan menjalankan fungsi ajax;
                    jQuery('.update').on('click',function() {
                 
                        var par   = $(this).parents('form');
                 
                        var numb  = par.find('[data="numb"]');
                        
                        var Data_total = par.serialize();
                        
                        /*
                            Pencocokan apakah yang dimasukan kedalam inputan adalah angka atau bukan
                            dengan fungsi RegExp Javasicript
                            ref : http://www.w3schools.com/js/js_obj_regexp.asp 
                                  http://www.w3schools.com/jsref/jsref_obj_regexp.asp
                        */
                        numb.each(function() {

                            var patrn = new RegExp('[0-9]');

                            var value = $(this).val();
                            
                            if(patrn.test(value) === false){
                            
                                $('.alert-danger').html('<p><b><i>Parameter yang Anda Masukan Bukan angka</i></b></p>').removeClass('hide');
                                
                                $(this).css('border', '1px solid red');  

                            }else{
                 
                                $('.alert-danger').html('').hide();
                 
                                $(this).attr('data-val','true');
                            }


                        });

                        var valid = par.find('[data-val]'); 

                        /*
                            menyamakan jumlah tag beratribut "data-val" dengan tag beratribut "data" bervalue "numb". melalui fungsi length; 
                        */
                        
                        if( valid.length == numb.length ){

                            $.ajax({
                                url:par.attr('action'),
                                type:'post',
                                data:Data_total,
                                success:function(result){
                                    if(result == 'sukses'){
                                
                                        alert('Update Sukses');
                                        location.reload(true); // me-reload halaman saat ini;
                                
                                    }else{
                                
                                        alert(result);
                                
                                    }
                                }

                            });

                            return false;
                        }
                    });

                }else{
                    
                    jQuery('.view-dana').html(result);

                }
                
            }
        }); return false;
    });
    
    /*
        Lihat Form input
    */
    jQuery('.lihat').click(function(event) {
       
       var button = $(this);
       var parnt  = $(this).parents('form'); // menentukan induk dari tag yang di klik. dalam hal ini adalah tag <form>
       var Url  = parnt.attr('action'); // mengambil value dari attribut "action" dari tag <form>

       /*
        Mengambil seluruh nilai dan mengubahnya menjadi array
        ref :http://www.w3schools.com/jquery/ajax_serialize.asp
       */ 
       var Data = parnt.serialize();

       var nama = parnt.find('[name="user"]'); // mengambil / mencari tag dengan attribut "name" dan bervalue "user";
       var dana = parnt.find('[name="dana"]'); // mengambil / mencari tag dengan attribut "name" dan bervalue "dana";

       /*
         Pengecekan value dari menu select bernilai / tidak
       */
       if(nama.val() == "" ){ 

             alert('Tentukan User terlebih dahulu.');

        }else if(dana.val() ==''){

             alert('Tentukan Jenis');

        }else{

       $.ajax({
            url:Url,
            type:'post',
            data:Data,
            success:function(result){
                jQuery('.input-total').html(result);

                /*
                    menyembunyikan Tombol "Lihat";
                */
                button.animate({
                    'opacity': 0},
                    400, function() {
                    $(this).css('visibility','hidden');
                });
                
                /*
                    menambah tombol "Simpan" di Inputan Total Dana
                */
                jQuery('.simpan-btn').html('<div class="btn btn-primary btn-sm simpan ">Simpan</div>');

                // Dan jika tombol "Simpan" di klik akan menjalankan fungsi ajax;
                jQuery('.simpan').on('click',function() {
             
                    var par   = $(this).parents('form');
             
                    var numb  = par.find('[data="numb"]');
                    
                    var Data_total = par.serialize();
                    
                    /*
                        Pencocokan apakah yang dimasukan kedalam inputan adalah angka atau bukan
                        dengan fungsi RegExp Javasicript
                        ref : http://www.w3schools.com/js/js_obj_regexp.asp 
                              http://www.w3schools.com/jsref/jsref_obj_regexp.asp
                    */
                    numb.each(function() {

                        var patrn = new RegExp('[0-9]');

                        var value = $(this).val();
                        
                        if(patrn.test(value) === false){
                        
                            $('.alert-danger').html('<p><b><i>Parameter yang Anda Masukan Bukan angka</i></b></p>').removeClass('hide');
                            
                            $(this).css('border', '1px solid red');  

                        }else{
             
                            $('.alert-danger').html('').hide();
             
                            $(this).attr('data-val','true');
                        }


                    });

                    var valid = par.find('[data-val]'); 

                    /*
                        menyamakan jumlah tag beratribut "data-val" dengan tag beratribut "data" bervalue "numb". melalui fungsi length; 
                    */
                    
                    if( valid.length == numb.length ){

                        $.ajax({
                            url:par.attr('action'),
                            type:'post',
                            data:Data_total,
                            success:function(result){
                                if(result == 'sukses'){
                            
                                    alert('Insert Sukses');
                                    location.reload(true); // me-reload halaman saat ini;
                            
                                }else{
                            
                                    alert(result);
                            
                                }
                            }

                        });

                        return false;
                    }
                });

            }
       });
       return false;
   }
   
    });
    
    /*
        tombol "lihat" akan muncul kembali saat  tag "select" di klik;
    */
    jQuery('select').click(function(event) {
        jQuery('.lihat').animate({
                    'opacity': 1},
                    400, function() {
                    $(this).css('visibility','visible');
                });

    });
    
});
</script>