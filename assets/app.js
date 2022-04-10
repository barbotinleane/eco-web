// CSS imports
import './styles/global.scss';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery');

require('bootstrap');


$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

import './search';
