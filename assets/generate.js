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

function generate_qr_bonus_card(count){
    let output_qr_url = document.querySelector('.qr-generate-page .barcode-area img');
    output_qr_url.classList.add('loading')
    let xhr = new XMLHttpRequest();
    xhr.open("POST", `${window.location.origin}/wp-admin/admin-ajax.php`, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    let params = 'action=generate_qr_bonus_card&count=' + count;
    xhr.onreadystatechange = () => {

        if(xhr.readyState == 4) {
            if(xhr.status == 200) {
                output_qr_url.setAttribute('src',xhr.responseText);
                setTimeout(function (){
                    output_qr_url.classList.remove('loading')
                },2500)
            }else{
                console.log("SOME ERROR HTTP");
                console.log(xhr.responseText);

            }

        }
    };

    xhr.send(params);
}