// CSS imports
import './styles/global.scss';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery');

require('bootstrap');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

import './search';
import './formation';
