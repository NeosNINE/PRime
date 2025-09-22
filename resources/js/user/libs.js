require('../bootstrap');

require('moment');
require('../libs/revered-helpers');
require('../guest/components/custom-select');
const AirDatepicker = require('air-datepicker');
require('air-datepicker/air-datepicker.css');

// Bootstrap
window.bootstrap = require('../libs/bootstrap.bundle.js');

// Initialize AirDatepicker for elements with data-input-date attribute
document.querySelectorAll('[data-input-date]').forEach((el) => {
    new AirDatepicker(el);
});

// Make AirDatepicker globally available - handle both default and named exports
console.log('AirDatepicker module:', AirDatepicker);
console.log('AirDatepicker type:', typeof AirDatepicker);
console.log('AirDatepicker constructor:', typeof AirDatepicker === 'function');

if (AirDatepicker && AirDatepicker.default) {
    console.log('Using AirDatepicker.default');
    window.AirDatepicker = AirDatepicker.default;
} else if (AirDatepicker && typeof AirDatepicker === 'function') {
    console.log('Using AirDatepicker directly');
    window.AirDatepicker = AirDatepicker;
} else {
    console.error('AirDatepicker failed to load or is not a constructor');
    console.log('AirDatepicker:', AirDatepicker);
}