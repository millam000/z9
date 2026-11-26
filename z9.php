<?php declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';
require_once __DIR__ . '/db_config.php';

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$currentUser   = (string) $_SESSION['username'];
$currentAvatar = (string) ($_SESSION['avatar'] ?? 'media/avatars/default.png');
$isAdmin       = ($currentUser === 'admin');
session_write_close();

$link = getDbConnection();
$initialUsers = [];
$resUsers = mysqli_query($link, "SELECT id, username, avatar FROM users ORDER BY username ASC");
if ($resUsers) {
    while ($u = mysqli_fetch_assoc($resUsers)) {
        $initialUsers[] = [
            'id'       => (int) $u['id'],
            'username' => $u['username'],
            'avatar'   => !empty($u['avatar']) ? $u['avatar'] : 'media/avatars/default.png',
        ];
    }
}

$initialMessages = [];
if ($isAdmin) {
    $resMsg = mysqli_query($link, "SELECT idk, datetime, message, user, recipient FROM messages ORDER BY idk ASC");
} else {
    $stmt = mysqli_prepare($link, "SELECT idk, datetime, message, user, recipient FROM messages WHERE user = ? OR recipient = ? ORDER BY idk ASC");
    mysqli_stmt_bind_param($stmt, 'ss', $currentUser, $currentUser);
    mysqli_stmt_execute($stmt);
    $resMsg = mysqli_stmt_get_result($stmt);
}
if ($resMsg) {
    while ($m = mysqli_fetch_assoc($resMsg)) {
        $initialMessages[] = [
            'idk'       => (int) $m['idk'],
            'datetime'  => $m['datetime'],
            'message'   => $m['message'],
            'user'      => $m['user'],
            'recipient' => $m['recipient'],
        ];
    }
}
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Komunikator – Portal Z9</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">

    <style>
        .chat-layout {
            height: calc(100vh - 105px);
            min-height: 520px;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.1);
            border: 1px solid #dee2e6;
        }
        .chat-panel {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #ffffff;
        }
        .contacts-panel {
            border-left: 1px solid #e9ecef;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .contacts-list {
            overflow-y: auto;
            flex-grow: 1;
        }
        .contact-item {
            cursor: pointer;
            transition: background-color .15s ease;
            border-bottom: 1px solid #f1f3f5;
            user-select: none;
        }
        .contact-item:hover {
            background-color: #e9ecef;
        }
        .contact-item.active {
            background-color: #e7f1ff;
            border-right: 4px solid #0d6efd;
        }
        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1.25rem;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .msg-row {
            display: flex;
            flex-direction: column;
            max-width: 72%;
        }
        .msg-row.mine {
            align-self: flex-end;
            align-items: flex-end;
        }
        .msg-row.theirs {
            align-self: flex-start;
            align-items: flex-start;
        }
        .msg-bubble {
            padding: 0.6rem 0.9rem;
            border-radius: 16px;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,.08);
            position: relative;
        }
        .msg-row.mine .msg-bubble {
            background-color: #0d6efd;
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .msg-row.theirs .msg-bubble {
            background-color: #fff;
            color: #212529;
            border: 1px solid #e4e6eb;
            border-bottom-left-radius: 4px;
        }
        .msg-meta {
            font-size: 0.72rem;
            color: #6c757d;
            margin-top: 2px;
        }
        .chat-media-img {
            max-width: 280px;
            max-height: 260px;
            border-radius: 10px;
            display: block;
            cursor: pointer;
        }
        .chat-media-video {
            max-width: 320px;
            max-height: 240px;
            border-radius: 10px;
            display: block;
            background: #000;
        }
        .chat-media-audio {
            max-width: 270px;
            height: 38px;
            display: block;
        }
        .tts-btn {
            font-size: 0.75rem;
            opacity: 0.75;
            cursor: pointer;
            border: none;
            background: transparent;
            padding: 0 4px;
        }
        .tts-btn:hover {
            opacity: 1;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="twoj_js.js"></script>
</head>

<body onload="myLoadHeader()" class="has-header d-flex flex-column min-vh-100">
    <div id="myHeader"></div>

    <main class="container-fluid px-3 py-2 flex-grow-1" style="max-width: 1400px;">
        <div class="row g-0 chat-layout">
            <!-- LEWY PANEL: OKNO CZATU -->
            <div class="col-md-8 col-lg-9 chat-panel">
                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <img id="activeChatAvatar" src="media/avatars/default.png" width="40" height="40" class="rounded-circle object-fit-cover border" alt="avatar">
                        <div>
                            <h2 class="h6 fw-bold mb-0" id="activeChatName">Wybierz rozmówcę z listy po prawej</h2>
                            <small class="text-muted" id="activeChatSubtitle">Kliknij użytkownika na liście po prawej stronie, aby pisać</small>
                        </div>
                    </div>
                </div>

                <!-- Lista wiadomości -->
                <div class="chat-messages" id="chatMessages">
                    <div class="text-center text-muted my-auto">
                        <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                        Wybierz użytkownika z listy po prawej stronie, aby rozpocząć rozmowę.
                    </div>
                </div>

                <!-- Dolny pasek wysyłania -->
                <div class="p-3 bg-white border-top">
                    <form id="messageForm" class="d-flex align-items-center gap-2">
                        <input type="hidden" id="recipientInput" name="recipient" value="">

                        <label for="fileInput" class="btn btn-outline-secondary mb-0" title="Wyślij obrazek, muzykę (MP3), wideo (MP4) lub inny plik">
                            <i class="bi bi-paperclip fs-5"></i>
                        </label>
                        <input type="file" id="fileInput" name="fileToUpload" class="d-none">

                        <!-- <button type="button" id="btnSpeechToText" class="btn btn-outline-secondary" title="Dyktuj wiadomość głosowo (STT)">
                            <i class="bi bi-mic-fill"></i>
                        </button> -->

                        <input type="text" id="postInput" name="post" class="form-control" placeholder="Napisz wiadomość..." autocomplete="off">

                        <button type="submit" id="btnSendMsg" class="btn btn-primary px-4" title="Wyślij">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                    <div id="chatStatusMsg" class="small mt-1 text-muted"></div>
                </div>
            </div>

            <!-- PRAWY PANEL: LISTA UŻYTKOWNIKÓW -->
            <div class="col-md-4 col-lg-3 contacts-panel">
                <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?= htmlspecialchars($currentAvatar) ?>" width="38" height="38" class="rounded-circle object-fit-cover border" alt="avatar">
                        <div>
                            <div class="fw-bold lh-1"><?= htmlspecialchars($currentUser) ?></div>
                            <small class="text-success"><i class="bi bi-circle-fill" style="font-size:8px;"></i> Zalogowany</small>
                        </div>
                    </div>
                </div>

                <div class="p-2 border-bottom bg-white">
                    <input type="text" id="searchContact" class="form-control form-control-sm" placeholder="Szukaj użytkownika...">
                </div>

                <div class="contacts-list" id="contactsList"></div>
            </div>
        </div>
    </main>

    <?php require_once 'footer.php'; ?>

    <script>
    const CURRENT_USER = <?= json_encode($currentUser, JSON_UNESCAPED_UNICODE) ?>;
    const IS_ADMIN     = <?= $isAdmin ? 'true' : 'false' ?>;

    let allUsers = <?= json_encode($initialUsers, JSON_UNESCAPED_UNICODE) ?>;
    let allMessages = <?= json_encode($initialMessages, JSON_UNESCAPED_UNICODE) ?>;
    let lastKnownId = allMessages.length > 0 ? Math.max(...allMessages.map(m => m.idk)) : 0;

    let selectedUser = '';
    let renderedConversationUser = '';
    let renderedMessageIds = new Set();
    let lastContactsSig = '';
    let isPolling = false;

    function getUserBubbleStyle(username) {
        if (username === CURRENT_USER) return '';
        let hash = 0;
        for (let i = 0; i < username.length; i++) {
            hash = username.charCodeAt(i) + ((hash << 5) - hash);
        }
        const hue = Math.abs(hash) % 360;
        return `background-color: hsl(${hue}, 65%, 92%); border-color: hsl(${hue}, 55%, 80%); color: #111;`;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function formatMessageContent(rawMsg) {
        const trimmed = rawMsg.trim();
        const filePattern = /^[a-zA-Z0-9_-]+\/[^<>:"|?*\r\n]+$/;
        if (filePattern.test(trimmed)) {
            const ext = trimmed.split('.').pop().toLowerCase();
            const safeUrl = encodeURI(trimmed);
            const fileName = escapeHtml(trimmed.split('/').pop());

            if (['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(ext)) {
                return `<a href="${safeUrl}" target="_blank">
                            <img src="${safeUrl}" alt="${fileName}" class="chat-media-img" loading="lazy">
                        </a>`;
            }
            if (ext === 'mp3' || ext === 'wav' || ext === 'ogg') {
                return `<div class="small mb-1"><i class="bi bi-music-note-beamed me-1"></i>${fileName}</div>
                        <audio controls preload="none" class="chat-media-audio">
                            <source src="${safeUrl}" type="audio/mpeg">
                            Twoja przeglądarka nie obsługuje odtwarzacza audio.
                        </audio>`;
            }
            if (ext === 'mp4' || ext === 'webm') {
                return `<video controls autoplay muted playsinline loop preload="metadata" class="chat-media-video">
                            <source src="${safeUrl}" type="video/mp4">
                            Twoja przeglądarka nie obsługuje odtwarzacza wideo.
                        </video>`;
            }
            if (ext === 'pdf') {
                return `<a href="${safeUrl}" target="_blank" class="btn btn-sm btn-light border">
                            <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Otwórz PDF: ${fileName}
                        </a>`;
            }
            return `<a href="${safeUrl}" target="_blank" class="btn btn-sm btn-light border">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Pobierz: ${fileName}
                    </a>`;
        }

        const safeText = escapeHtml(trimmed);
        return `<span>${safeText}</span>
                <button type="button" class="tts-btn" onclick="speakText(this)" data-text="${safeText}" title="Odsłuchaj wiadomość">
                    <i class="bi bi-volume-up-fill"></i>
                </button>`;
    }

    function speakText(btn) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(btn.dataset.text || '');
        utterance.lang = 'pl-PL';
        window.speechSynthesis.speak(utterance);
    }

    function renderContacts(force = false) {
        const filter = ($('#searchContact').val() || '').toLowerCase().trim();
        const contactsSig = JSON.stringify({
            u: allUsers,
            sel: selectedUser,
            f: filter,
            lastId: lastKnownId
        });
        if (!force && contactsSig === lastContactsSig) return;
        lastContactsSig = contactsSig;

        const listEl = $('#contactsList');
        listEl.empty();

        if (IS_ADMIN) {
            const activeAll = (selectedUser === '__ALL__') ? 'active' : '';
            const allItem = $(`
                <div class="p-3 d-flex align-items-center gap-3 contact-item ${activeAll}">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-danger text-truncate">Wszyscy użytkownicy</div>
                        <small class="text-muted">Tryb podglądu Admina</small>
                    </div>
                </div>
            `);
            allItem.on('click', function () {
                selectRecipient('__ALL__');
            });
            listEl.append(allItem);
        }

        const otherUsers = allUsers.filter(u => u.username !== CURRENT_USER);
        $('#usersCountBadge').text(otherUsers.length);

        if (otherUsers.length === 0 && !IS_ADMIN) {
            listEl.append('<div class="p-3 text-center text-muted small">Brak innych zarejestrowanych użytkowników.</div>');
        }

        otherUsers.forEach(u => {
            if (filter && !u.username.toLowerCase().includes(filter)) return;

            const convMsgs = allMessages.filter(m =>
                (m.user === CURRENT_USER && m.recipient === u.username) ||
                (m.user === u.username && m.recipient === CURRENT_USER)
            );
            const lastMsg = convMsgs.length ? convMsgs[convMsgs.length - 1].message : 'Kliknij, aby napisać';
            const isActive = (selectedUser === u.username) ? 'active' : '';

            const itemEl = $(`
                <div class="p-3 d-flex align-items-center gap-3 contact-item ${isActive}">
                    <img src="${escapeHtml(u.avatar)}" width="44" height="44" class="rounded-circle object-fit-cover border flex-shrink-0" alt="avatar">
                    <div class="overflow-hidden flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-truncate">${escapeHtml(u.username)}</span>
                            <span class="badge bg-light text-dark border">${convMsgs.length}</span>
                        </div>
                        <div class="small text-muted text-truncate">${escapeHtml(lastMsg)}</div>
                    </div>
                </div>
            `);

            itemEl.on('click', function () {
                selectRecipient(u.username);
            });

            listEl.append(itemEl);
        });

        if (!selectedUser) {
            if (IS_ADMIN) {
                selectRecipient('__ALL__');
            } else if (otherUsers.length > 0) {
                selectRecipient(otherUsers[0].username);
            }
        }
    }

    function selectRecipient(username) {
        selectedUser = username;

        if (username === '__ALL__') {
            $('#recipientInput').val('');
            $('#activeChatName').text('Wszystkie konwersacje (Administrator)');
            $('#activeChatSubtitle').text('Podgląd wszystkich wiadomości w bazie danych (wybierz użytkownika z listy po prawej, aby wysłać wiadomość)');
            $('#activeChatAvatar').attr('src', 'media/avatars/default.png');
        } else {
            const uObj = allUsers.find(u => u.username === username);
            $('#recipientInput').val(username);
            $('#activeChatName').text(username);
            $('#activeChatSubtitle').text('Rozmowa z użytkownikiem: ' + username);
            $('#activeChatAvatar').attr('src', uObj ? uObj.avatar : 'media/avatars/default.png');
        }

        renderContacts(true);
        renderMessages();
    }

    function createMessageElement(m) {
        const isMine = (m.user === CURRENT_USER);
        const rowClass = isMine ? 'mine' : 'theirs';
        const customStyle = getUserBubbleStyle(m.user);
        const contentHtml = formatMessageContent(m.message);
        const headerLabel = (selectedUser === '__ALL__')
            ? `${escapeHtml(m.user)} → ${escapeHtml(m.recipient)}`
            : escapeHtml(m.user);

        const wrapper = document.createElement('div');
        wrapper.className = `msg-row ${rowClass}`;
        wrapper.dataset.idk = String(m.idk);
        wrapper.innerHTML = `
            <div class="msg-bubble" style="${customStyle}">
                ${contentHtml}
            </div>
            <div class="msg-meta">
                <strong>${headerLabel}</strong> • ${escapeHtml(m.datetime)}
            </div>
        `;
        return wrapper;
    }

    function renderMessages() {
        const box = document.getElementById('chatMessages');
        if (!selectedUser) return;

        let filtered = [];
        if (selectedUser === '__ALL__' && IS_ADMIN) {
            filtered = allMessages;
        } else {
            filtered = allMessages.filter(m =>
                (m.user === CURRENT_USER && m.recipient === selectedUser) ||
                (m.user === selectedUser && m.recipient === CURRENT_USER)
            );
        }

        if (renderedConversationUser !== selectedUser) {
            renderedConversationUser = selectedUser;
            renderedMessageIds.clear();
            box.innerHTML = '';
        }

        if (filtered.length === 0) {
            if (!box.querySelector('.empty-msg-info')) {
                box.innerHTML = `
                    <div class="text-center text-muted my-auto empty-msg-info">
                        <i class="bi bi-chat-heart fs-1 d-block mb-2"></i>
                        Brak wiadomości w tej rozmowie. Napisz pierwszą wiadomość!
                    </div>`;
            }
            return;
        }

        const emptyInfo = box.querySelector('.empty-msg-info');
        if (emptyInfo) emptyInfo.remove();

        let addedNew = false;
        filtered.forEach(m => {
            if (!renderedMessageIds.has(m.idk)) {
                renderedMessageIds.add(m.idk);
                box.appendChild(createMessageElement(m));
                addedNew = true;
            }
        });

        if (addedNew) {
            box.scrollTop = box.scrollHeight;
        }
    }

    // Lekkie pobieranie tylko nowych wiadomości przez AJAX
    function pollMessages() {
        if (isPolling) return;
        isPolling = true;

        fetch('get_messages.php?last_id=' + encodeURIComponent(lastKnownId), {
            credentials: 'same-origin',
            cache: 'no-store'
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'ok') {
                $('#connectionBadge').removeClass('bg-warning text-dark').addClass('bg-success text-white').text('AJAX Aktywny');

                if (Array.isArray(data.users)) {
                    allUsers = data.users;
                }

                if (Array.isArray(data.messages) && data.messages.length > 0) {
                    const existingIds = new Set(allMessages.map(m => m.idk));
                    data.messages.forEach(m => {
                        if (!existingIds.has(m.idk)) {
                            allMessages.push(m);
                            if (m.idk > lastKnownId) {
                                lastKnownId = m.idk;
                            }
                        }
                    });
                }

                renderContacts();
                renderMessages();
            }
        })
        .catch(() => {
            $('#connectionBadge').removeClass('bg-success text-white').addClass('bg-warning text-dark').text('Ponawianie...');
        })
        .finally(() => {
            isPolling = false;
        });
    }

    renderContacts();
    renderMessages();

    // Odpytywanie co 1.2 sekundy (tylko jeśli poprzednie żądanie się zakończyło)
    setInterval(pollMessages, 1200);

    $('#searchContact').on('input', function () {
        renderContacts(true);
    });

    // Wysyłanie wiadomości tekstowej
    $('#messageForm').on('submit', function (e) {
        e.preventDefault();
        const recipient = $('#recipientInput').val();
        const postInput = $('#postInput');
        const post = postInput.val().trim();

        if (!recipient) {
            $('#chatStatusMsg').html('<span class="text-danger">Kliknij odbiorcę na liście po prawej stronie!</span>');
            return;
        }
        if (!post) return;

        postInput.val('');

        const body = new URLSearchParams();
        body.append('recipient', recipient);
        body.append('post', post);

        fetch('send_message.php', {
            method: 'POST',
            body: body,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'ok') {
                $('#chatStatusMsg').text('');
                pollMessages(); // Natychmiastowe pobranie nowej wiadomości
            } else {
                $('#chatStatusMsg').html('<span class="text-danger">' + escapeHtml(res.message || 'Błąd wysyłania') + '</span>');
            }
        })
        .catch(() => {
            $('#chatStatusMsg').html('<span class="text-danger">Błąd połączenia podczas wysyłania wiadomości</span>');
        });
    });

    // Wysyłanie pliku (spinacz)
    $('#fileInput').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        const recipient = $('#recipientInput').val();
        if (!recipient) {
            $('#chatStatusMsg').html('<span class="text-danger">Najpierw kliknij odbiorcę pliku na liście po prawej stronie!</span>');
            this.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('fileToUpload', file);
        formData.append('recipient', recipient);

        $('#chatStatusMsg').html('<span class="text-primary"><i class="bi bi-hourglass-split"></i> Przesyłanie pliku: ' + escapeHtml(file.name) + '...</span>');

        fetch('upload_msg.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'ok') {
                $('#chatStatusMsg').html('<span class="text-success">Wysłano plik: ' + escapeHtml(file.name) + '</span>');
                pollMessages();
            } else {
                $('#chatStatusMsg').html('<span class="text-danger">' + escapeHtml(res.message || 'Błąd przesyłania') + '</span>');
            }
            $('#fileInput').val('');
        })
        .catch(() => {
            $('#chatStatusMsg').html('<span class="text-danger">Błąd przesyłania pliku</span>');
            $('#fileInput').val('');
        });
    });

    // Speech-to-Text
    $('#btnSpeechToText').on('click', function () {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert('Twoja przeglądarka nie obsługuje rozpoznawania mowy.');
            return;
        }
        const recognition = new SpeechRecognition();
        recognition.lang = 'pl-PL';
        recognition.interimResults = false;

        const btn = $(this);
        btn.removeClass('btn-outline-secondary').addClass('btn-danger');
        $('#chatStatusMsg').html('<span class="text-danger">Słucham... mów teraz.</span>');

        recognition.onresult = function (event) {
            const transcript = event.results[0][0].transcript;
            const currentVal = $('#postInput').val();
            $('#postInput').val(currentVal ? currentVal + ' ' + transcript : transcript);
        };
        recognition.onerror = function () {
            $('#chatStatusMsg').html('<span class="text-danger">Błąd rozpoznawania mowy.</span>');
        };
        recognition.onend = function () {
            btn.removeClass('btn-danger').addClass('btn-outline-secondary');
            $('#chatStatusMsg').text('');
        };
        recognition.start();
    });
    </script>
</body>

</html>