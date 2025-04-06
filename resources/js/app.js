import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.Echo.channel('parsing').listen('ParsingProcess', (event, data) => {
    $('#parsed_row').text(event.id);
})
