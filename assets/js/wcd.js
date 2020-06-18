function wcd_get_cookie( name ) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function wcd_set_cookie(name, value, options = {}) {

    options = {
        path: '/',
        samesite: 'lax',
        // при необходимости добавьте другие значения по умолчанию
        ...options
    };

    if (options.expires instanceof Date) {
        options.expires = options.expires.toUTCString();
    }

    let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

    for (let optionKey in options) {
        updatedCookie += "; " + optionKey;
        let optionValue = options[optionKey];
        if (optionValue !== true) {
            updatedCookie += "=" + optionValue;
        }
    }

    document.cookie = updatedCookie;
}

function wcd_remove_cookie(name) {
    wcd_set_cookie(name, "", {
        'max-age': -1
    });
}

jQuery( function( $ ) {
    let last_visited = wcd_get_cookie( 'wcd_last_visited' );
    let visited_date = new Date().getTime() / 1000;
    visited_date = Math.floor( visited_date );
    wcd_set_cookie( 'wcd_last_visited', visited_date, { 'max-age' : 2592000 });

    let is_show_message = wcd_get_cookie( 'wcd_show_message' );
    if ( typeof is_show_message != "undefined" ) {
        wcd_remove_cookie( 'wcd_show_message' );
        alert( 'спасибо, что вернулись, вот вам скидка ' + wcd.visited_discount + '%' );
    }

    let leave_discount = wcd_get_cookie( 'wcd_leaving_discount' );
    if ( typeof leave_discount == "undefined" ) {
        $('body').one('mouseleave', function () {
            wcd_set_cookie('wcd_leaving_discount', 1, {'max-age': 2592000});
            alert( 'Стойте, вот вам скидка ' + wcd.leaving_discount + '%' );
            // тут ещё бы страницу обновить чтобы скидку было видно. window.location.reload(false);
        });
    }
} );
