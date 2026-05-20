import L from 'leaflet';

export const TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
export const TILE_ATTR = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

export const DEFAULT_CENTER = [2.9213, 101.6543]; // Cyberjaya, Selangor (default)

export function buildBaseMap(elementId, { center = DEFAULT_CENTER, zoom = 13 } = {}) {
    const map = L.map(elementId, { zoomControl: true, scrollWheelZoom: false }).setView(center, zoom);
    L.tileLayer(TILE_URL, { attribution: TILE_ATTR, maxZoom: 19 }).addTo(map);
    map.on('focus', () => map.scrollWheelZoom.enable());
    map.on('blur', () => map.scrollWheelZoom.disable());
    return map;
}

export const polygonStyle = (status = 'available') => {
    const palette = {
        available:    { color: '#5fa31c', fillColor: '#9bd844' }, // brand lime
        occupied:     { color: '#0284c7', fillColor: '#38bdf8' },
        under_review: { color: '#d97706', fillColor: '#fbbf24' },
        inactive:     { color: '#525a4d', fillColor: '#9ba294' },
    };
    const p = palette[String(status || '').toLowerCase().replace(/\s+/g, '_')] || palette.available;
    return { color: p.color, weight: 2, fillColor: p.fillColor, fillOpacity: 0.35 };
};

export function featureToHtml(props) {
    const lines = [
        `<div class="text-xs"><div class="font-semibold text-ink-900">${props.name ?? ''}</div>`,
        props.reference_no ? `<div class="font-mono text-[10px] text-ink-500">${props.reference_no}</div>` : '',
        props.type   ? `<div class="text-ink-600">Type: ${props.type}</div>` : '',
        props.status ? `<div class="text-ink-600">Status: ${props.status}</div>` : '',
        props.url    ? `<a href="${props.url}" class="mt-1 inline-block font-medium text-brand-700 hover:text-brand-800">View →</a>` : '',
        '</div>'
    ];
    return lines.join('');
}

export function renderFeatureCollection(map, fc) {
    if (!fc || !fc.features || fc.features.length === 0) return null;
    const layer = L.geoJSON(fc, {
        style: (f) => polygonStyle(f.properties?.status_raw || f.properties?.status),
        onEachFeature: (feature, lyr) => {
            lyr.bindPopup(featureToHtml(feature.properties || {}));
        }
    }).addTo(map);
    try {
        map.fitBounds(layer.getBounds(), { padding: [20, 20], maxZoom: 17 });
    } catch (_) {}
    return layer;
}
