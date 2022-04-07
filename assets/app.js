// CSS imports
import './styles/global.scss';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery');

require('bootstrap');


$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

var myModal = document.getElementById('myModal')
var myInput = document.getElementById('myInput')

myModal.addEventListener('shown.bs.modal', function () {
    myInput.focus()
})
