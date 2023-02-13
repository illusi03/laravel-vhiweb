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

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }
    </style>

</head>

<body>

    <table width="100%">
        <tr>
            <td valign="top"><img src="" alt="" width="150" /></td>
            <td align="right">
                <p style="max-width: 200px; align-self: flex-end; text-align: right;">
                    {{ config('consts.company_name') }}
                </p>
                <p style="max-width: 200px; align-self: flex-end; text-align: right;">
                    {{ config('consts.company_address') }}
                </p>
            </td>
        </tr>

    </table>

    <table width="100%">
        <tr>
            <td>
                {{ Arr::get($order, 'creator.name') }} 
                ( {{ Arr::get($order, 'creator.email') }} )
            </td>
        </tr>
        <tr>
            <td colspan="2">Nomor : {{  Arr::get($order, 'number'); }}</td>
        </tr>
        <tr>
            <td colspan="2">Catatan : {{  Arr::get($order, 'note'); }}</td>
        </tr>
    </table>

    <br />

    <table width="100%">
        <thead style="background-color: lightgray;">
            <tr>
                <th>#</th>
                <th>Deskripsi</th>
                <th>Qty</th>
                <th>Harga Satuan (Rp.)</th>
                <th>Total (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $orderItems = Arr::get($order, 'orderItems');
            foreach ($orderItems as $key => $value) {
                $no = $key + 1;
                $price = Arr::get($value, 'discount_price');
                if($price == null) $price = Arr::get($value, 'price');
                $productName = Arr::get($value, 'product.name');
                $productCode = Arr::get($value, 'product.code');
                $product = "$productName - $productCode";
                $qty = Arr::get($value, 'qty');
                $totalPrice = Arr::get($value, 'total_price');
                echo "<tr>";
                echo "<td scope='row'>$no</td>";
                echo "<td>$product</td=>";
                echo "<td align='right'>$qty</td>";
                echo "<td align='right'>$price</td>";
                echo "<td align='right'>$totalPrice</td>";
                echo "</tr>";
            }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td align="right">Subtotal Item (Rp.)</td>
                <td align="right">{{ Arr::get($order, 'item_price'); }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td align="right">Ongkos Kirim (Rp.)</td>
                <td align="right">{{ Arr::get($order, 'shipping_price'); }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td align="right">Pajak (Rp.)</td>
                <td align="right">{{ Arr::get($order, 'tax_price'); }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td align="right">Diskon (Rp.)</td>
                <td align="right">{{ Arr::get($order, 'discount_price'); }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td align="right">Grand Total (Rp.)</td>
                <td align="right" class="gray">{{ Arr::get($order, 'total_price') }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>