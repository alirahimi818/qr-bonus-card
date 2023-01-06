window.addEventListener("load", (event) => {
    let checksum = qrbcGetQueryStringParameterByName('checksum');
    let user = getCookie('bonus_user');
    let checksum_status = getCookie('qr_bonus_response_status');
    let checksum_message = getCookie('qr_bonus_response_message');
    if (!user) {
        cookie_qr_bonus_card_user()
    }

    if(checksum){
        cookie_qr_bonus_card_checksum(checksum)
    }

    if (checksum_status && checksum_message) {
        let message_el = document.querySelector('.qr-bonus-checksum-result-message');
        message_el.innerHTML = checksum_status == 'success' ? "<div class='success-color'>" + checksum_message + "</div>" : "<div class='failed-color'>" + checksum_message + "</div>"
        deleteCookie('qr_bonus_response_status')
        deleteCookie('qr_bonus_response_message')
    }
});

function qrbcGetQueryStringParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function deleteCookie(cname) {
    let name = cname + "=; ";
    document.cookie = cname + "expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

function cookie_qr_bonus_card_user() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let params = 'action=cookie_qr_bonus_card_user';
    xhr.onreadystatechange = () => {

        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                setCookie('bonus_user', xhr.responseText, 365)
            } else {
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);

            }

        }
    };

    xhr.send(params);
}

function cookie_qr_bonus_card_checksum(checksum) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let user = getCookie('bonus_user');
    let params = 'action=cookie_qr_bonus_card_checksum&checksum=' + checksum + '&bonus_user=' + user;
    xhr.onreadystatechange = () => {

        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                try {
                    let obj = JSON.parse(xhr.responseText);
                    setCookie('qr_bonus_response_status', obj.status, 1)
                    setCookie('qr_bonus_response_message', obj.message, 1)
                    window.location.replace(obj.url);
                } catch (e) {
                    console.log(e);
                }

            } else {
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);

            }

        }
        window.location.replace(window.location.href.split('?')[0]);
    };

    xhr.send(params);
}