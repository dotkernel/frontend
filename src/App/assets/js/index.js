// Load jQuery and Bootstrap
try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {}

require('./components/_sidebar');
require('./components/_accordion');

