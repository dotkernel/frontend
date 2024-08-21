// Load jQuery and Bootstrap
try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
    require('bootstrap-fileinput');
    require('bootstrap-slider');
    require('jquery-mousewheel');

    window.toastr = require('toastr');
} catch (e) {
}

require('./components/_contact');
require('./components/_avatar');
require('./components/_profile');
