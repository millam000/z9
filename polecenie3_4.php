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
    <title>Miłosz Lamański – 3.4 (wszystkie zdarzenia)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="twoj_js.js"></script>
</head>
<body onload="myLoadHeader()">
    <br>
    
<div id='myHeader'></div>

<main class="container-fluid my-4 px-3" style="max-width:1500px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0"><i class="bi bi-people"></i> Goście portalu – wszystkie zdarzenia</h5>
            <span class="small opacity-75">Wejścia • Zalogowania • Błędy logowania</span>
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

            $result = mysqli_query($link, "SELECT * FROM goscieportalu ORDER BY datetime DESC");
            ?>

            <div class="row g-2 mb-3">
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Akcja</label>
                    <select id="filterAction" class="form-select form-select-sm">
                        <option value="">Wszystkie</option>
                        <option value="visit">Wejście</option>
                        <option value="login_ok">Zalogowano</option>
                        <option value="login_fail">Błąd logowania</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Cookies</label>
                    <select id="filterCookies" class="form-select form-select-sm">
                        <option value="">Wszystkie</option>
                        <option value="^Tak$">Tak</option>
                        <option value="^Nie$">Nie</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Java</label>
                    <select id="filterJava" class="form-select form-select-sm">
                        <option value="">Wszystkie</option>
                        <option value="^Tak$">Tak</option>
                        <option value="^Nie$">Nie</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Kraj</label>
                    <select id="filterCountry" class="form-select form-select-sm">
                        <option value="">Wszystkie</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-x-circle"></i> Wyczyść filtry
                    </button>
                </div>
            </div>

            <table id="visitorsTable" class="table table-striped table-hover mb-0 nowrap" style="width:100%;">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Akcja</th>
                        <th>Login</th>
                        <th>Data</th>
                        <th>Przeglądarka</th>
                        <th>Ekran / Okno / Kolory</th>
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
                    $act     = $row['action'] ?? 'visit';
                    $isVisit = ($act === 'visit');
                    $uname   = (string) ($row['username_attempt'] ?? '');
                    $rank    = ['visit' => 1, 'login_ok' => 2, 'login_fail' => 3][$act] ?? 0;
                    $cls     = ['visit' => 'bg-primary', 'login_ok' => 'bg-success', 'login_fail' => 'bg-danger'][$act] ?? 'bg-secondary';
                    $label   = ['visit' => 'Wejście', 'login_ok' => 'Zalogowano', 'login_fail' => 'Błąd logowania'][$act] ?? $act;
                    $icon    = ['visit' => 'bi-eye-fill', 'login_ok' => 'bi-check-circle-fill', 'login_fail' => 'bi-x-circle-fill'][$act] ?? 'bi-question';
                    ?>
                    <tr data-action="<?= htmlspecialchars($act) ?>" data-country="<?= htmlspecialchars($country) ?>">
                        <td><code><?= htmlspecialchars($ip) ?></code></td>
                        <td data-order="<?= $rank ?>">
                            <span class="badge <?= $cls ?>"><i class="bi <?= $icon ?>"></i> <?= $label ?></span>
                        </td>
                        <td><?= $uname !== '' ? htmlspecialchars($uname) : '<span class="text-muted">—</span>' ?></td>
                        <td data-order="<?= htmlspecialchars($row['datetime']) ?>">
                            <?= htmlspecialchars(date('d.m.Y H:i', strtotime($row['datetime']))) ?>
                        </td>
                        <td><?= htmlspecialchars($row['browser']) ?></td>
                        <td data-order="<?= htmlspecialchars($isVisit ? $row['screen_res'] : '') ?>">
                            <?php if ($isVisit && $row['screen_res'] !== ''): ?>
                                <span class="stacked d-inline-block">
                                    <?= htmlspecialchars($row['screen_res']) ?><br>
                                    <?= htmlspecialchars($row['window_res']) ?><br>
                                    <?= (int) $row['screen_colors'] ?> bit
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $isVisit ? ((int)$row['cookies_enabled'] ? 'Tak' : 'Nie') : '<span class="text-muted">—</span>' ?></td>
                        <td class="text-center"><?= $isVisit ? ((int)$row['java_enabled']    ? 'Tak' : 'Nie') : '<span class="text-muted">—</span>' ?></td>
                        <td><?= $row['language'] !== '' ? htmlspecialchars($row['language']) : '<span class="text-muted">—</span>' ?></td>
                        <td><?= htmlspecialchars($geo) ?></td>
                        <td>
                            <?php if ($loc): ?>
                                <a href="https://www.google.pl/maps/place/<?= htmlspecialchars($loc) ?>"
                                   target="_blank" class="btn btn-sm btn-outline-primary py-0 px-1"
                                   title="Pokaż na Google Maps">
                                    <i class="bi bi-geo-alt"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?php mysqli_close($link); ?>

        </div>
    </div>
</main>

<script>
$(document).ready(function () {
    const table = $('#visitorsTable').DataTable({
        responsive: true,
        order: [[3, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Wszystkie']],
        language: {
            search: 'Szukaj:', lengthMenu: 'Pokaż _MENU_ wpisów',
            info: 'Wyświetlanie _START_ do _END_ z _TOTAL_ wpisów',
            infoEmpty: 'Brak wpisów',
            infoFiltered: '(filtrowane z _MAX_)',
            zeroRecords: 'Brak pasujących wpisów',
            emptyTable: 'Brak danych',
            paginate: { first: '«', last: '»', next: '›', previous: '‹' }
        },
        columnDefs: [
            { responsivePriority: 1, targets: 0 },   // IP
            { responsivePriority: 2, targets: 1 },   // Akcja
            { responsivePriority: 3, targets: 3 },   // Data
            { responsivePriority: 4, targets: 2 },   // Login
            { responsivePriority: 5, targets: 10 },  // Mapa
            { responsivePriority: 100, targets: 5 }, // Ekran/Okno/Kolory
            { responsivePriority: 90, targets: 6 },  // Cookies
            { responsivePriority: 90, targets: 7 },  // Java
            { targets: [6, 7], className: 'text-center' }
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

    $.fn.dataTable.ext.search.push(function (settings, data, index) {
        const a = $('#filterAction').val();
        if (!a) return true;
        const node = table.row(index).node();
        return node && node.dataset.action === a;
    });
    $('#filterAction').on('change', () => table.draw());

    $('#filterCookies').on('change', function () {
        table.column(6).search(this.value, true, false).draw();
    });
    $('#filterJava').on('change', function () {
        table.column(7).search(this.value, true, false).draw();
    });
    $.fn.dataTable.ext.search.push(function (settings, data, index) {
        const c = $('#filterCountry').val();
        if (!c) return true;
        const node = table.row(index).node();
        return node && node.dataset.country === c;
    });
    $('#filterCountry').on('change', () => table.draw());

    $('#resetFilters').on('click', function () {
        $('#filterAction, #filterCookies, #filterJava, #filterCountry').val('');
        table.search('').columns().search('').draw();
    });
});
</script>

<?php require_once 'footer.php'; ?>
</body>
</html>