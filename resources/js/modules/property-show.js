import { buildBaseMap, renderFeatureCollection } from './map-helpers.js';

export function initPropertyShowMap() {
    const data = window.__propertyMap || {};
    const map = buildBaseMap('property-map');
    renderFeatureCollection(map, data.geoJson ? { type: 'FeatureCollection', features: [data.geoJson] } : null);
}
