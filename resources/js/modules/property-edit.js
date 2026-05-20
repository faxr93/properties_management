import L from 'leaflet';
import 'leaflet-draw';
import { buildBaseMap } from './map-helpers.js';

export function initPropertyEditMap() {
    const data = window.__propertyEditMap || {};
    const map = buildBaseMap('property-edit-map', { zoom: 14 });

    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    const drawControl = new L.Control.Draw({
        edit: { featureGroup: drawnItems, edit: true, remove: true },
        draw: {
            polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#5fa31c', weight: 2, fillColor: '#9bd844', fillOpacity: 0.30 } },
            polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false,
        }
    });
    map.addControl(drawControl);

    const boundaryField = document.getElementById('field-boundary');
    const locationField = document.getElementById('field-location');
    const statusEl = document.querySelector('[data-map-status]');
    const clearBtn = document.querySelector('[data-map-action="clear"]');

    const updateFields = () => {
        const layers = drawnItems.getLayers();
        if (layers.length === 0) {
            boundaryField.value = '';
            locationField.value = '';
            if (statusEl) statusEl.textContent = 'No polygon drawn yet.';
            return;
        }
        const layer = layers[layers.length - 1];
        const geoJson = layer.toGeoJSON();
        boundaryField.value = JSON.stringify(geoJson.geometry);

        const center = layer.getBounds().getCenter();
        locationField.value = JSON.stringify({ type: 'Point', coordinates: [center.lng, center.lat] });

        if (statusEl) {
            const vertices = geoJson.geometry.coordinates[0]?.length || 0;
            statusEl.textContent = `Polygon with ${vertices} vertices · centroid ${center.lat.toFixed(5)}, ${center.lng.toFixed(5)}`;
        }
    };

    map.on(L.Draw.Event.CREATED, (e) => {
        drawnItems.clearLayers(); // single boundary
        drawnItems.addLayer(e.layer);
        updateFields();
    });
    map.on(L.Draw.Event.EDITED, updateFields);
    map.on(L.Draw.Event.DELETED, updateFields);

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            drawnItems.clearLayers();
            updateFields();
        });
    }

    // Hydrate existing polygon if editing.
    if (data.boundary && data.boundary.type === 'Polygon') {
        const layer = L.geoJSON(data.boundary, {
            style: { color: '#5fa31c', weight: 2, fillColor: '#9bd844', fillOpacity: 0.30 }
        });
        layer.eachLayer((l) => drawnItems.addLayer(l));
        try {
            map.fitBounds(drawnItems.getBounds(), { padding: [20, 20], maxZoom: 17 });
        } catch (_) {}
        updateFields();
    } else if (data.location && data.location.type === 'Point') {
        map.setView([data.location.coordinates[1], data.location.coordinates[0]], 16);
    }
}
