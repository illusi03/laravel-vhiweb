<?php
use Illuminate\Support\Arr;
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice Order</title>

    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }
    </style>

</head>

<body>
    <h4>Hai, {{ Arr::get($order, 'creator.name') }}</h4>
    <p>
        Kamu sudah menerima pembayaran untuk pemesananmu. 
        Bukti pembayaran mu ada dilampiran, ya. Setelah pesanan dikonfirmasi, 
        kami akan mengirim pesanan mu ke tempat tujuanmu.
    </p>
    <p>
        Butuh Bantuan ? Kunjungi <a href="#">Pusat Bantuan</a> atau <a href="#">Hubungi Customer Care</a>
    </p>
    <p>
        Cheers, <br/>
        <a href="">{{ config('consts.frontend_url') }}</a>
    </p>
</body>

<footer>

</footer>

</html>