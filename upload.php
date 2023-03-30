<?php
if (isset($_FILES['resim'])) {
    $yuklenemeyenler = array(); //yüklenemeyen ve hatası dönen resimleri bu dizide tutacağız.

    $klasor = "upload/"; //yükleyeceğimiz klasörü belirledik.

    //Artık resimlerimiz dizi olarak geldiği için bir döngü ile tek tek kontrol ve kayıt etmemiz gerekiyor.
    $resim_sayisi = count($_FILES['resim']['name']); //kaç tane resim geldiğini öğrendik.
    for ($i = 0; $i < $resim_sayisi; $i++) {
        //resim sayısı kadar döngüye soktuk.

        $resimBoyutu = $_FILES['resim']['size'][$i]; //döngü içerisindeki resmin boyutunu öğrendik.
        $tip = $_FILES['resim']['type'][$i]; //resim tipini öğrendik.
        $resimAdi = $_FILES['resim']['name'][$i]; //resmin adını öğrendik.

        if ($tip == 'image/jpeg' || $tip == 'image/jpg' || $tip == 'image/png') { //uzantısnın kontrolünü sağladık. sadece .jpg ve .png yükleyebilmesi için.
            if (move_uploaded_file($_FILES["resim"]["tmp_name"][$i], $klasor . "/" . $_FILES['resim']['name'][$i])) {
                //tmp_name ile resmi bulduk ve nereye, hangi isimle yukleneceğini belirleyip yükledik.
                //yükleme işlemi başarılı olursa dilediğiniz bir olayı gerçekleştirebilirsiniz.
            } else
                $yuklenemeyenler[] = $_FILES['resim']['name'][$i] . " BİLİNMİYOR";
        } else {
            $yuklenemeyenler[] = $_FILES['resim']['name'][$i] . " UZANTI";
        }
    }
    if (count($yuklenemeyenler) > 0) {
        echo "Aşağıdaki Resimler Yüklenemedi. <br />";
        var_dump($yuklenemeyenler);
    } else
        echo "TÜM RESİMLER BAŞARILI BİR ŞEKİLDE YÜKLENDİ.";
}
?>
<!doctype html>
<html>

<head>
    <title>Form Sayfası | Resim Yükleme</title>
</head>

<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="resim[]" multiple="multiple" />
        <!-- burada multiple ifadesi bilgisayar üzerinden seçimin çoklu olacağını belirtir. -->
        <!-- name="resim[]" nameden sonra gelen [] ibaresi post edildiği zaman seçilen değerlerin dizi halinde post edilmesini sağlar. -->
        <button type="submit">YÜKLE</button>
    </form>
</body>

</html>