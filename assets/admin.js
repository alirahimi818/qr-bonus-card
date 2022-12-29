function table_pagination(all_count, per_page, current_page, all_pages_name = 'pages') {
    let nav_url = window.location.href;
    if (nav_url.indexOf("pagination") > -1) {
        let nav_url_arr = nav_url.split('&pagination=');
        nav_url = nav_url_arr[0] + '&pagination='
    } else {
        nav_url = nav_url + '&pagination=';
    }
    all_count = Math.ceil(Number(all_count) / Number(per_page));
    current_page = Number(current_page);
    let prev_page = current_page == 1 ? '1' : current_page - 1;
    let next_page = current_page >= all_count ? all_count : current_page + 1;
    jQuery('.pagination-table').after('<div class="tablenav bottom"><div class="tablenav-pages"><span class="displaying-num">' + all_count + ' ' + all_pages_name + '</span>\n' +
        '<a class="first-page button" href="' + nav_url + '1"><span\n' +
        '            aria-hidden="true">«</span></a>\n' +
        '<a class="prev-page button" href="' + nav_url + prev_page + '"><span\n' +
        '            aria-hidden="true">‹</span></a>\n' +
        '<span id="table-paging" class="paging-input"><span class="tablenav-paging-text"><span\n' +
        '                class="current_page">' + current_page + '</span> - <span class="total-pages">' + all_count + '</span></span></span>\n' +
        '<a class="next-page button" href="' + nav_url + next_page + '"><span\n' +
        '            aria-hidden="true">›</span></a>\n' +
        '<a class="last-page button" href="' + nav_url + all_count + '"><span\n' +
        '            aria-hidden="true">»</span></a></span></div></div>');
}