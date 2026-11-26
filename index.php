<?php declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
	header('Location: login.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Miłosz Lamański – Zadanie 9 (Komunikator)</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link rel="stylesheet" type="text/css" href="twoj_css.css">
	<style>
		.service-card {
			transition: transform .18s ease, box-shadow .18s ease;
			border: none;
			border-radius: 14px;
		}
		.service-card:hover {
			transform: translateY(-4px);
			box-shadow: 0 1rem 2rem rgba(0,0,0,.18) !important;
		}
		.service-card .icon-wrap {
			width: 56px; height: 56px;
			display: flex; align-items: center; justify-content: center;
			border-radius: 12px;
			background: rgba(255,255,255,.2);
		}
	</style>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
		crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script type="text/javascript" src="twoj_js.js"></script>
</head>

<body onload="myLoadHeader()" class="has-header d-flex flex-column min-vh-100">
	<div id='myHeader'></div>
	<main class="container py-5 flex-grow-1" style="max-width: 960px;">
		<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
			<h1 class="h3 fw-bold mb-0">Zadanie 9</h1>
			<a href="z9.php" class="btn btn-primary">
				<i class="bi bi-chat-dots-fill me-1"></i> Otwórz Komunikator
			</a>
		</div>

		<div class="row g-4">
			<div class="col-md-6">
				<a href="z9.php" class="text-decoration-none">
					<div class="card service-card bg-primary text-white shadow h-100">
						<div class="card-body d-flex align-items-center gap-3 p-4">
							<div class="icon-wrap flex-shrink-0">
								<i class="bi bi-chat-dots-fill fs-2"></i>
							</div>
							<div>
								<h2 class="h5 fw-bold mb-1">Komunikator</h2>
								<p class="small mb-0 opacity-75">Czat na żywo, obrazki, audio, wideo i pliki</p>
							</div>
						</div>
					</div>
				</a>
			</div>
		</div>
	</main>
	<?php require_once 'footer.php'; ?>
</body>

</html>