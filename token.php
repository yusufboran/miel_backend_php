<?php
function tokenGenerator($dbconn)
{
    $now = time();
    $next_time = $now + (60 * 60 * 8);
    $token = bin2hex(random_bytes(8));
    $sql = "INSERT INTO token_list (final_time, \"token\") VALUES('" . $next_time . "', '" . $token . "');";
    // client.query(sql);
    $result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
    return $token;
}

function checkTokenValidity($dbconn, $token)
{

    try {
        $sql = "SELECT final_time FROM public.token_list where token = '" . $token . "';";
        $result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
       
        if (!$result || pg_num_rows($result) == 0) {
            return false;
        }
       
        $final_time = intval(pg_fetch_result($result, 0, 'final_time'));
    
        if ($final_time > time()) {
            return "true";
        }
        return false;
    } catch (error) {
        return false;
    }
}