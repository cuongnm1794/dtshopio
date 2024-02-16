<?php
// Lấy giá trị max_execution_time hiện tại
$maxExecutionTime = ini_get('max_execution_time');

if ($maxExecutionTime === false) {
    echo "max_execution_time is not set in PHP configuration.";
} else {
    echo "Current max_execution_time: $maxExecutionTime seconds";
}

// Thêm mã JavaScript để hiển thị bộ đếm thời gian còn lại
echo '<script>
    var maxExecutionTime = ' . $maxExecutionTime . ';
    var startTime = ' . time() . ';
    
    function updateTimer() {
        var currentTime = ' . time() . ';
        var elapsedTime = currentTime - startTime;
        var remainingTime = maxExecutionTime - elapsedTime;
        
        if (remainingTime <= 0) {
            document.getElementById("timer").innerHTML = "Timeout!";
        } else {
            document.getElementById("timer").innerHTML = "Time remaining: " + remainingTime + " seconds";
            setTimeout(updateTimer, 1000);
        }
    }
    
    window.onload = updateTimer;
</script>';

// Thêm một phần tử HTML để hiển thị bộ đếm thời gian
echo '<p id="timer">Time remaining: ' . $maxExecutionTime . ' seconds</p>';

// Tạo vòng lặp để kiểm tra timeout
set_time_limit(0);

$startTime = time();

while (true) {
    if (time() - $startTime >= $maxExecutionTime) {
        echo "Script has reached the max_execution_time of $maxExecutionTime seconds and will exit now.";
        break;
    }
}

echo "Script completed.";

// Khôi phục giới hạn thời gian thực hiện về giá trị ban đầu
set_time_limit($maxExecutionTime);
?>