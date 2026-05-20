import { buildBaseMap, renderFeatureCollection } from './map-helpers.js';
import { Chart } from 'chart.js';

export function initDashboard() {
    const data = window.__dashboard || {};
    const map = buildBaseMap('dashboard-map');
    renderFeatureCollection(map, data.mapGeoJson);

    const ctx = document.getElementById('valuationChart');
    if (ctx && data.valuationTrend) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.valuationTrend.map((d) => d.label),
                datasets: [{
                    data: data.valuationTrend.map((d) => d.value),
                    borderColor: '#5fa31c',
                    backgroundColor: 'rgba(155,216,68,0.20)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#9bd844',
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#64748b' } },
                    y: {
                        ticks: {
                            font: { size: 10 }, color: '#64748b',
                            callback: (v) => 'RM ' + Intl.NumberFormat('en-MY', { notation: 'compact', maximumFractionDigits: 1 }).format(v),
                        },
                        grid: { color: '#f1f5f9' },
                    },
                },
            },
        });
    }
}
