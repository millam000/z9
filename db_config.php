<?php
declare(strict_types=1);

function getDbConnection(): mysqli
{
    $link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z9");
    if (!$link) {
        http_response_code(500);
        die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
    }
    mysqli_set_charset($link, "utf8mb4");
    return $link;
}