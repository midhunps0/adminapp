import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist'
import page from '@/components/page';
import filepond from '@/components/filepond';
import * as utils from '@/components/utils';
import progressbar from '@/components/progressbar';
import 'filepond/dist/filepond.min.css';

Alpine.plugin(persist)

window.Alpine = Alpine;

window.sleep = function(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
};


Alpine.store('app', {
    xpages: [],
    pageloading: false
});

Alpine.data('initPage', page);
Alpine.data('filepond', filepond);
Alpine.data('progressBar', progressbar);


Alpine.start();
