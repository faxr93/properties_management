import './bootstrap';
import Alpine from 'alpinejs';
import L from 'leaflet';
import 'leaflet-draw';
import { Chart, registerables } from 'chart.js';
import { initDashboard } from './modules/dashboard.js';
import { initPropertiesIndexMap } from './modules/properties-map.js';
import { initPropertyShowMap } from './modules/property-show.js';
import { initPropertyEditMap } from './modules/property-edit.js';

Chart.register(...registerables);

// Fix Leaflet's default marker icon path resolution under Vite.
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl:       markerIcon,
    shadowUrl:     markerShadow,
});

window.L = L;
window.Chart = Chart;
window.Alpine = Alpine;
Alpine.start();

const ready = (fn) => (document.readyState !== 'loading'
    ? fn()
    : document.addEventListener('DOMContentLoaded', fn));

ready(() => {
    if (document.getElementById('dashboard-map')) {
        initDashboard();
    }
    if (document.getElementById('properties-map')) {
        initPropertiesIndexMap();
    }
    if (document.getElementById('property-map')) {
        initPropertyShowMap();
    }
    if (document.getElementById('property-edit-map')) {
        initPropertyEditMap();
    }
});
