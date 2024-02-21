<?php

function sendMessage($content)
{
    // Đặt token bot của bạn
    $botToken = "6356909772:AAE-JpJ_Ycc2ujW5cFa3f6WLT6Od-B1yTvg";

    // Đặt ID group
    $chatId = "-4183140168";

    // Thông tin cần gửi

    // URL API Telegram
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";

    // Cấu hình CURL
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ["chat_id" => $chatId, "text" => $content, 'parse_mode' => 'HTML']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Gửi yêu cầu
    $response = curl_exec($ch);

    // Đóng kết nối
    curl_close($ch);

    // Xử lý kết quả
    if ($response === false) {
        echo "Lỗi khi gửi thông tin: " . curl_error($ch);
    } else {
        echo "Thông tin đã được gửi thành công!";
    }
}
