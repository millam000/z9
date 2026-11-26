<?php declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Miłosz Lamański – 3.5 (unikalne IP + akcje)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="twoj_js.js"></script>
</head>
<body onload="myLoadHeader()">
    <div id='myHeader'></div>

    <main class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Goście portalu – unikalne IP + akcje</h5>
            </div>
            <div class="card-body">

                <?php
                function geo_lookup(string $ip): ?object
                {
                    $dir = __DIR__ . '/cache';
                    if (!is_dir($dir)) @mkdir($dir, 0777, true);
                    $f = $dir . '/geo_' . md5($ip) . '.json';
                    if (is_file($f) && (time() - filemtime($f) < 86400)) {
                        $d = json_decode((string) file_get_contents($f));
                        if ($d && !empty($d->country)) return $d;
                    }
                    $key = '4eec4678c2d69e75e61a5a3b5e8edc85d5d5f05e54c85c809cee662c9df92cbc';
                    $url = 'https://ip.spyte.xyz/geo/' . urlencode($ip) . '?key=' . urlencode($key);
                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => 2,
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
                    ]);
                    $resp = curl_exec($ch);
                    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if ($resp === false || $code !== 200) return null;
                    $d = json_decode($resp);
                    if ($d && !empty($d->country)) @file_put_contents($f, $resp);
                    return $d ?: null;
                }

                $link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z9");
                if (!$link) { echo "Błąd: " . mysqli_connect_error(); exit; }
                mysqli_query($link, "SET NAMES 'utf8'");

                // Liczymy osobno: wejścia / logowania OK / logowania nieudane.
                // Wiersz danych pokazuje OSTATNIE WEJŚCIE danej IP (a nie ostatnie
                // zdarzenie), żeby Ekran/Okno/Kolory miały sensowne wartości.
                $sql = "
                    SELECT g.*,
                           cnt.n_visits,
                           cnt.n_ok,
                           cnt.n_fail
                    FROM goscieportalu g
                    INNER JOIN (
                        SELECT ipaddress,
                               SUM(CASE WHEN action = 'visit'      THEN 1 ELSE 0 END) AS n_visits,
                               SUM(CASE WHEN action = 'login_ok'   THEN 1 ELSE 0 END) AS n_ok,
                               SUM(CASE WHEN action = 'login_fail' THEN 1 ELSE 0 END) AS n_fail,
                               MAX(datetime) AS max_dt,
                               MAX(CASE WHEN action = 'visit' THEN datetime END) AS max_visit_dt
                        FROM goscieportalu
                        GROUP BY ipaddress
                    ) cnt
                        ON cnt.ipaddress = g.ipaddress
                       AND cnt.max_visit_dt = g.datetime
                       AND g.action = 'visit'
                    ORDER BY g.datetime DESC
                ";
                $result = mysqli_query($link, $sql);
                ?>

                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Cookies</label>
                        <select id="filterCookies" class="form-select form-select-sm">
                            <option value="">Wszystkie</option>
                            <option value="^Tak$">Tak</option>
                            <option value="^Nie$">Nie</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Java</label>
                        <select id="filterJava" class="form-select form-select-sm">
                            <option value="">Wszystkie</option>
                            <option value="^Tak$">Tak</option>
                            <option value="^Nie$">Nie</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Kraj</label>
                        <select id="filterCountry" class="form-select form-select-sm">
                            <option value="">Wszystkie</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-x-circle"></i> Wyczyść filtry
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="visitorsTable" class="table table-striped table-hover mb-0" style="width:100%;">
                        <thead>
                            <tr>
                                <th>IP</th>
                                <th>Wejścia</th>
                                <th>Log. OK</th>
                                <th>Log. błędy</th>
                                <th>Data</th>
                                <th>Przeglądarka</th>
                                <th>Ekran</th>
                                <th>Okno</th>
                                <th>Kolory</th>
                                <th>Cookies</th>
                                <th>Java</th>
                                <th>Język</th>
                                <th>Geolokalizacja</th>
                                <th>Mapa</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $ip = $row['ipaddress'];
                            $details = geo_lookup($ip);
                            $loc     = $details->loc     ?? '';
                            $city    = $details->city    ?? '';
                            $region  = $details->region  ?? '';
                            $country = $details->country ?? '';
                            $geo     = ($country || $city) ? trim("$city, $region, $country", ', ') : 'Brak';
                            ?>
                            <tr data-country="<?= htmlspecialchars($country) ?>">
                                <td><?= htmlspecialchars($ip) ?></td>
                                <td class="text-center" data-order="<?= (int) $row['n_visits'] ?>">
                                    <span class="badge bg-primary"><?= (int) $row['n_visits'] ?></span>
                                </td>
                                <td class="text-center" data-order="<?= (int) $row['n_ok'] ?>">
                                    <span class="badge bg-success"><?= (int) $row['n_ok'] ?></span>
                                </td>
                                <td class="text-center" data-order="<?= (int) $row['n_fail'] ?>">
                                    <span class="badge bg-danger"><?= (int) $row['n_fail'] ?></span>
                                </td>
                                <td data-order="<?= htmlspecialchars($row['datetime']) ?>"><?= htmlspecialchars($row['datetime']) ?></td>
                                <td><?= htmlspecialchars($row['browser']) ?></td>
                                <td><?= htmlspecialchars($row['screen_res']) ?></td>
                                <td><?= htmlspecialchars($row['window_res']) ?></td>
                                <td><?= htmlspecialchars($row['screen_colors']) ?></td>
                                <td><?= (int) $row['cookies_enabled'] ? 'Tak' : 'Nie' ?></td>
                                <td><?= (int) $row['java_enabled']    ? 'Tak' : 'Nie' ?></td>
                                <td><?= htmlspecialchars($row['language']) ?></td>
                                <td><?= htmlspecialchars($geo) ?></td>
                                <td>
                                    <?php if ($loc): ?>
                                        <a href="https://www.google.pl/maps/place/<?= htmlspecialchars($loc) ?>"
                                           target="_blank" class="btn btn-sm btn-outline-primary"
                                           title="Pokaż na Google Maps">
                                            <i class="bi bi-geo-alt"></i> Mapa
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <?php mysqli_close($link); ?>

            </div>
        </div>
    </main>

    <script>
    $(document).ready(function () {
        const table = $('#visitorsTable').DataTable({
            order: [[4, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Wszystkie']],
            language: {
                search: 'Szukaj:', lengthMenu: 'Pokaż _MENU_ wpisów',
                info: 'Wyświetlanie _START_ do _END_ z _TOTAL_ wpisów',
                infoEmpty: 'Brak wpisów',
                infoFiltered: '(filtrowane z _MAX_ wszystkich wpisów)',
                zeroRecords: 'Nie znaleziono pasujących wpisów',
                emptyTable: 'Brak danych w tabeli',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' }
            },
            columnDefs: [
                { targets: [1, 2, 3, 9, 10], className: 'text-center' }
            ],
            initComplete: function () {
                const countries = new Set();
                document.querySelectorAll('#visitorsTable tbody tr').forEach(tr => {
                    if (tr.dataset.country) countries.add(tr.dataset.country);
                });
                const sel = $('#filterCountry');
                [...countries].sort().forEach(c => sel.append(new Option(c, c)));
            }
        });

        $('#filterCookies').on('change', function () {
            table.column(9).search(this.value, true, false).draw();
        });
        $('#filterJava').on('change', function () {
            table.column(10).search(this.value, true, false).draw();
        });
        $.fn.dataTable.ext.search.push(function (settings, data, index) {
            const wanted = $('#filterCountry').val();
            if (!wanted) return true;
            const node = table.row(index).node();
            return node && node.dataset.country === wanted;
        });
        $('#filterCountry').on('change', () => table.draw());
        $('#resetFilters').on('click', function () {
            $('#filterCookies, #filterJava, #filterCountry').val('');
            table.search('').columns().search('').draw();
        });
    });
    </script>

    <?php require_once 'footer.php'; ?>
</body>
</html>