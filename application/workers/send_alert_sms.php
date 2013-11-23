<?php

function send_alert_sms($job) {
    try {
        setlocale(LC_CTYPE, "en_US.UTF-8");
        $shell_string = 'php /home/sosbeacon/sosbeacon-api-v2/application/GearmanDispatch_SosBeaconAlert.php alert sms ' . escapeshellarg($job->workload());
        shell_exec($shell_string);
    } catch (Exception $e) {}
    return null;
}