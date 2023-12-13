<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>條碼掃描器</title>
    <style>
        /* 你的樣式在這裡 */
    </style>
</head>
<body>
    <form action="index.php" method="POST" id="barcodeForm">
        <div id="scan" class="viewport"></div>
        <div id="barcodeDisplay">條碼： </div>
        <!-- 將條碼隱藏在表單中，以便提交 -->
        <input type="hidden" name="barcode" id="hiddenBarcodeInput">
    </form>

    <script src="https://cdn.rawgit.com/serratus/quaggaJS/0.12.1/dist/quagga.min.js"></script>
    <script>
        var barcodeDetected = false;

        function startQuagga() {
            // Quagga 初始化代碼
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector("#scan"),
                    constraints: {
                        width: 480,
                        height: 320,
                        facingMode: "environment"
                    }
                },
                decoder: {
                    readers: ['code_128_reader']
                }
            }, function (err) {
                if (err) {
                    console.error(err);
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function (result) {
                if (!barcodeDetected) {
                    barcodeDetected = true;
                    var code = result.codeResult.code;
                    console.log('檢測到條碼：' + code);

                    // 顯示條碼和成功消息
                    document.getElementById('barcodeDisplay').innerText = '條碼：' + code + ' 成功';
                    setTimeout(function () {
                    // 將條碼資料設定到隱藏的 input 元素中
                        document.getElementById('hiddenBarcodeInput').value = code;

                    // 提交表單
                        var form = document.getElementById('barcodeForm');
                        form.submit();

                    // 重置 barcodeDetected 標誌
                        barcodeDetected = false;
                    }, 1000); // 延遲 3 秒
                }
            });
        }

        // 開始第一次掃描
        startQuagga();
    </script>
    <?php

if (isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    // 連接mysql
    $servername = "localhost";
    $username = "id21642130_testrobot";
    $password = "!Testrobot520";
    $dbname = "id21642130_testrobot";
    $port=3306;
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    // check
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    date_default_timezone_set('America/Chicago');
    $currentDateTime = date("Y-m-d H:i:s");
    // 插入資料
    $sql = "INSERT INTO barcode (code, date) VALUES ('$barcode', '$currentDateTime')";
    if ($conn->query($sql) === TRUE) {
        // 插入成功，重新導向回原本的檔案
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
    ?>
</body>
</html>