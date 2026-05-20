import { buildBaseMap, renderFeatureCollection } from './map-helpers.js';

export function initPropertiesIndexMap() {
    const data = window.__propertiesMap || {};
    const map = buildBaseMap('properties-map');
    renderFeatureCollection(map, data.geoJson);
}
