<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

function generate_invoice_pdf($order_id, $nama, $alamat, $telepon, $pembayaran, $product) {
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', false); // Tidak butuh load gambar eksternal

    $dompdf = new Dompdf($options);

    $html = '
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <div style="text-align: center; border-bottom: 3px solid #007bff; padding-bottom: 10px; margin-bottom: 20px;">
            <h1 style="margin: 10px 0 0 0; color: #007bff;">SEHAT SELALU</h1>
            <p style="margin: 0; font-size: 14px;">Solusi Kesehatan Anda</p>
        </div>

        <h2 style="text-align: center;">INVOICE PEMBELIAN</h2>
        <p><strong>No. Pesanan:</strong> #' . $order_id . '</p>
        <p><strong>Nama:</strong> ' . htmlspecialchars($nama) . '</p>
        <p><strong>Alamat:</strong> ' . htmlspecialchars($alamat) . '</p>
        <p><strong>Telepon:</strong> ' . htmlspecialchars($telepon) . '</p>
        <p><strong>Metode Pembayaran:</strong> ' . htmlspecialchars($pembayaran) . '</p>
        <hr>

        <h3>Detail Produk</h3>
        <table width="100%" border="1" cellspacing="0" cellpadding="8" style="border-collapse: collapse; font-size: 14px;">
            <tr style="background: #f2f2f2;">
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
            <tr>
                <td>' . htmlspecialchars($product['name']) . '</td>
                <td>Rp ' . number_format($product['price'], 0, ',', '.') . '</td>
                <td>1</td>
                <td>Rp ' . number_format($product['price'], 0, ',', '.') . '</td>
            </tr>
        </table>

        <h3 style="text-align: right;">Total: Rp ' . number_format($product['price'], 0, ',', '.') . '</h3>

        <p style="text-align:center; margin-top: 40px;">
            Terima kasih telah berbelanja di <strong>Sehat Selalu</strong>.<br>
            Semoga lekas sehat dan tetap semangat!
        </p>

        <div style="text-align:center; margin-top: 30px;">
            <small>Dicetak otomatis pada ' . date("d M Y H:i") . '</small>
        </div>
    </div>
    ';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("invoice_Sehat_Selalu$order_id.pdf", ["Attachment" => true]);
}
?>
