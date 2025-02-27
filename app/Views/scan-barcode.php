<!DOCTYPE html>
<html>
<head>
    <title>Scan Barcode</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>
    <h2>Scan Barcode Barang</h2>
    <div id="reader"></div>
    <p>Barcode: <span id="result"></span></p>

    <script>
        function onScanSuccess(decodedText) {
            document.getElementById("result").innerText = decodedText;
            window.location.href = "<?= base_url('barang-detail/update-by-barcode/') ?>" + decodedText;
        }

        let scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        scanner.render(onScanSuccess);
    </script>
</body>
</html>
