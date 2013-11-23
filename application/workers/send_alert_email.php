<?php

function send_alert_email($job) {
    try {
        setlocale(LC_CTYPE, "en_US.UTF-8");
        $shell_string = 'php /home/sosbeacon/sosbeacon-api-v2/application/GearmanDispatch_SosBeaconAlert.php alert email ' . escapeshellarg($job->workload());
        shell_exec($shell_string);
    } catch (Exception $e) {}
    return null;
}
