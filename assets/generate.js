get_latest_bonus_history();
document.querySelector('.qr-bonuses-history-refresh-btn').addEventListener("click", function (el) {
    get_latest_bonus_history();
})
document.querySelector('.bonus-today-history-toggle-btn').addEventListener("click", function (el) {
    let history_area = document.querySelector('.qr-bonuses-history-area');
    if (history_area.classList.contains('display-none')) {
        get_latest_bonus_history();
        history_area.classList.remove('display-none')
    } else {
        history_area.classList.add('display-none')
    }
})

let count_input = document.querySelector('.qr-generate-page .input-number');
document.querySelector('.qr-generate-page .control-btn.plus').addEventListener("click", function (el) {
    count_input.value = Number(count_input.value) + 1;
})

document.querySelector('.qr-generate-page .control-btn.minus').addEventListener("click", function (el) {
    if (count_input.value > 1) {
        count_input.value = Number(count_input.value) - 1;
    }
})

document.querySelector('.qr-generate-page .new-qr-btn').addEventListener("click", function (el) {
    qrbc_generate_qr_bonus_card(count_input.value)
})

function generate_qrcode(url) {
    let barcode_area = document.querySelector(".barcode-area .barcode-image");
    barcode_area.innerHTML = '';
    new QRCode(barcode_area, {
        text: url,
        width: 320,
        height: 320,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
    });
}

function qrbc_generate_qr_bonus_card(count) {
    let output_qr_url = document.querySelector('.qr-generate-page .barcode-area .barcode-image');
    output_qr_url.classList.add('loading');
    output_qr_url.insertAdjacentHTML("afterend", '<div class="loading-area"><div class="loading-spinner"></div></div>');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let params = 'action=qrbc_generate_qr_bonus_card&count=' + count;
    xhr.onreadystatechange = () => {

        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                generate_qrcode(xhr.responseText);
                setTimeout(function () {
                    get_latest_bonus_history()
                }, 60000)
            } else {
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);
                swal(xhr.responseText, '', "error");
            }
            setTimeout(function () {
                output_qr_url.classList.remove('loading');
                document.querySelector('.loading-area').remove();
            }, 1500)
        }
    };

    xhr.send(params);
}

function get_latest_bonus_history() {

    let output_table_el = document.querySelector('.qr-bonuses-history tbody');
    output_table_el.classList.add('loading');
    document.querySelector('.qr-bonuses-history').insertAdjacentHTML("afterend", '<div class="loading-area"><div class="loading-spinner"></div></div>');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let params = 'action=qrbc_latest_history_qr_bonus';
    xhr.onreadystatechange = () => {

        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                try {
                    if (xhr.responseText) {
                        let arr = JSON.parse(xhr.responseText);
                        let html = ``;
                        arr.forEach(function (v) {
                            html += `<tr>
                                        <td>${v.user_id}</td>
                                        <td>${v.created_at}</td>
                                        <td>${v.count}</td>
                                        <td>${v.active_bonus}</td>
                                        <td>${v.last_win}</td>
                                        <td>${v.win_count}</td>
                                        <td>`;
                            if (v.win_cards.length > 0) {
                                v.win_cards.forEach(function (val) {
                                    html += `<button type="button" class="mx-1" onclick="inactive_win_card(${val.id})"><svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M16 3.93552C14.795 3.33671 13.4368 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21C16.9706 21 21 16.9706 21 12C21 11.662 20.9814 11.3283 20.9451 11M21 5L12 14L9 11" stroke="#267d00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg></button>`;
                                })
                            } else {
                                html += '-'
                            }
                            `</td>
                                    </tr>`;
                        })
                        output_table_el.innerHTML = html;
                    }
                } catch (e) {
                    console.log(e);
                }
            } else {
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);
                swal(xhr.responseText, '', "error");
            }
            setTimeout(function () {
                output_table_el.classList.remove('loading');
                document.querySelector('.loading-area').remove();
            }, 1500)
        }
    };

    xhr.send(params);
}

function inactive_win_card(win_id) {
    let output_table_el = document.querySelector('.qr-bonuses-history tbody');
    output_table_el.classList.add('loading');
    document.querySelector('.qr-bonuses-history').insertAdjacentHTML("afterend", '<div class="loading-area"><div class="loading-spinner"></div></div>');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let params = 'action=qrbc_inactive_qr_bonus_card_win&win_id=' + win_id;
    xhr.onreadystatechange = () => {

        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    setTimeout(function () {
                        swal(xhr.responseText, '', "success");
                        get_latest_bonus_history();
                    }, 1000)
                }
            } else {
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);
                swal(xhr.responseText, '', "error");
            }
            setTimeout(function () {
                output_table_el.classList.remove('loading');
                document.querySelector('.loading-area').remove();
            }, 1500)
        }
    };

    xhr.send(params);
}