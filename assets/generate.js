get_today_bonus_history();
document.querySelector('.qr-bonuses-history-refresh-btn').addEventListener("click", function (el) {
    get_today_bonus_history();
})
document.querySelector('.bonus-today-history-toggle-btn').addEventListener("click", function (el) {
    let history_area = document.querySelector('.qr-bonuses-history-area');
    if (history_area.classList.contains('display-none')) {
        get_today_bonus_history();
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
    generate_qr_bonus_card(count_input.value)
})

function generate_qr_bonus_card(count) {
    let output_qr_url = document.querySelector('.qr-generate-page .barcode-area img');
    output_qr_url.classList.add('loading');
    output_qr_url.insertAdjacentHTML("afterend", '<div class="loading-area"><div class="loading-spinner"></div></div>');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let params = 'action=generate_qr_bonus_card&count=' + count;
    xhr.onreadystatechange = () => {

        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                output_qr_url.setAttribute('src', xhr.responseText);
                setTimeout(function () {
                    output_qr_url.classList.remove('loading');
                    document.querySelector('.loading-area').remove();
                }, 2500)
                setTimeout(function () {
                    get_today_bonus_history()
                }, 60000)
            } else {
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);

            }

        }
    };

    xhr.send(params);
}

function get_today_bonus_history() {

    let output_table_el = document.querySelector('.qr-bonuses-history tbody');
    output_table_el.classList.add('loading');
    document.querySelector('.qr-bonuses-history').insertAdjacentHTML("afterend", '<div class="loading-area"><div class="loading-spinner"></div></div>');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let params = 'action=today_history_qr_bonus';
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
                                        <td>${v.active_bonus}</td>
                                        <td>${v.last_win}</td>
                                        <td>${v.count}</td>
                                        <td>${v.created_at}</td>
                                    </tr>`;
                        })
                        output_table_el.innerHTML = html;
                        setTimeout(function () {
                            output_table_el.classList.remove('loading');
                            document.querySelector('.loading-area').remove();
                        }, 1500)
                    }
                } catch (e) {
                    console.log(e);
                }
            } else {
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);

            }

        }
    };

    xhr.send(params);
}