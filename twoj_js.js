function myLoadHeader() {
    $(document).ready(function () {
        $('#myHeader').load("header.php");
    });
}

function logVisitor() {
    // Zabezpieczenie przed podwójnym logowaniem (np. gdyby skrypt był dołączony 2x)
    if (window.__visitorLogged) return;
    window.__visitorLogged = true;

    const data = new URLSearchParams();
    data.append('screen_res', screen.width + 'x' + screen.height);
    data.append('window_res', window.innerWidth + 'x' + window.innerHeight);
    data.append('colors', screen.colorDepth);
    data.append('cookies', navigator.cookieEnabled ? 1 : 0);
    data.append('java', navigator.javaEnabled() ? 1 : 0);
    data.append('language', navigator.language || navigator.userLanguage || '');

    fetch('log_visitor.php', {
        method: 'POST',
        body: data,
        credentials: 'same-origin'
    }).catch(() => { });
}

window.addEventListener('load', logVisitor);