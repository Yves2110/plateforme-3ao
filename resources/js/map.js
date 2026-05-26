import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

window.L = L;

export const TYPE_COLORS = {
    'ONG': '#2D6A4F',
    'Réseau': '#D4A017',
    'Réseau OP': '#D4A017',
    'Institution': '#3B82F6',
    'Institution publique': '#3B82F6',
    'Recherche': '#8B5CF6',
    'Université': '#8B5CF6',
    'Entreprise': '#52B788',
    'Coopérative': '#84CC16',
    'Fondation': '#F97316',
    'Organisation paysanne': '#D4A017',
    'OP': '#D4A017',
};

const OSM_ATTRIBUTION = '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

export function waitForLeaflet(maxAttempts = 50) {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        const tick = () => {
            if (window.L?.map) {
                resolve(window.L);
                return;
            }
            if (++attempts >= maxAttempts) {
                reject(new Error('Leaflet failed to load'));
                return;
            }
            setTimeout(tick, 50);
        };
        tick();
    });
}

export function createOsmTileLayer() {
    return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: OSM_ATTRIBUTION,
        maxZoom: 19,
    });
}

window.actorMap = function actorMap(config) {
    return {
        map: null,
        cluster: null,
        actors: [],
        markers: {},
        loading: false,
        mapError: null,
        search: new URLSearchParams(location.search).get('q') || '',
        type: new URLSearchParams(location.search).get('type') || '',
        country: new URLSearchParams(location.search).get('country') || '',
        selected: null,
        detailOpen: false,
        selectedActor: null,
        detailLoading: false,

        get filteredCount() {
            return this.actors.length;
        },

        async init() {
            try {
                await waitForLeaflet();
                this.map = L.map('map', { zoomControl: true }).setView([12, -3], 5);
                createOsmTileLayer().addTo(this.map);

                this.cluster = L.markerClusterGroup({
                    showCoverageOnHover: false,
                    spiderfyOnMaxZoom: true,
                    chunkedLoading: true,
                    iconCreateFunction: (cluster) => L.divIcon({
                        html: `<div><span>${cluster.getChildCount()}</span></div>`,
                        className: 'marker-cluster marker-cluster-3ao',
                        iconSize: L.point(40, 40),
                    }),
                });
                this.map.addLayer(this.cluster);

                window.addEventListener('resize', () => this.invalidateMapSize());
                this.$nextTick(() => this.invalidateMapSize());
                await this.reload();
            } catch (e) {
                console.error('Carte:', e);
                this.mapError = config.i18n?.map_error || 'Unable to load the map.';
            }
        },

        invalidateMapSize() {
            if (this.map) {
                this.map.invalidateSize();
            }
        },

        makeIcon(type) {
            const color = TYPE_COLORS[type] || '#52B788';
            return L.divIcon({
                className: '',
                html: `<div style="width:28px;height:28px;background:${color};border:3px solid white;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 14],
                popupAnchor: [0, -14],
            });
        },

        makePopup(a) {
            const color = TYPE_COLORS[a.type] || '#52B788';
            const logo = a.logo
                ? `<img src="${a.logo}" alt="" style="width:40px;height:40px;border-radius:8px;object-fit:cover;flex-shrink:0">`
                : `<div style="width:40px;height:40px;border-radius:8px;background:${color};color:white;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px">${a.name.substring(0, 2).toUpperCase()}</div>`;
            const viewLabel = config.i18n?.view_profile || 'View profile →';
            return `
                <div style="min-width:220px;max-width:280px">
                    <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px">
                        ${logo}
                        <div style="min-width:0">
                            <p style="font-weight:700;font-size:14px;color:#1A1A2E;margin:0;line-height:1.2">${a.name}</p>
                            <p style="font-size:11px;color:#9CA3AF;margin:2px 0 0">${a.country}${a.city ? ' · ' + a.city : ''}</p>
                        </div>
                    </div>
                    <span style="display:inline-block;background:${color}20;color:${color};padding:2px 8px;border-radius:999px;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.5px">${a.type}</span>
                    ${a.excerpt ? `<p style="font-size:12px;color:#6B7280;margin:8px 0 0;line-height:1.4">${a.excerpt}</p>` : ''}
                    <a href="${a.url}" style="display:inline-block;margin-top:10px;font-size:12px;color:#2D6A4F;font-weight:700;text-decoration:none">${viewLabel}</a>
                </div>
            `;
        },

        async reload() {
            if (!this.cluster) return;
            this.loading = true;
            const params = new URLSearchParams();
            if (this.search) params.set('q', this.search);
            if (this.type) params.set('type', this.type);
            if (this.country) params.set('country', this.country);

            const newUrl = location.pathname + (params.toString() ? '?' + params : '');
            history.replaceState(null, '', newUrl);

            try {
                const res = await fetch(`${config.endpoint}?${params}`, { headers: { Accept: 'application/json' } });
                this.actors = await res.json();
                this.refreshMarkers();
            } catch (e) {
                console.error('Erreur chargement acteurs:', e);
            } finally {
                this.loading = false;
            }
        },

        refreshMarkers() {
            if (!this.cluster) return;
            this.cluster.clearLayers();
            this.markers = {};
            const layers = [];
            this.actors.forEach((a) => {
                const m = L.marker([a.lat, a.lng], { icon: this.makeIcon(a.type) })
                    .bindPopup(this.makePopup(a), { maxWidth: 300 });
                this.markers[a.id] = m;
                layers.push(m);
            });
            this.cluster.addLayers(layers);
        },

        focus(a) {
            this.selected = a.id;
            if (!this.map) return;
            this.map.setView([a.lat, a.lng], 11, { animate: true });
            setTimeout(() => {
                const m = this.markers[a.id];
                if (m) {
                    this.cluster.zoomToShowLayer(m, () => m.openPopup());
                }
            }, 400);
        },

        async openDetail(a) {
            this.focus(a);
            this.detailOpen = true;
            this.detailLoading = true;
            this.selectedActor = { id: a.id, name: a.name, url: a.url, html: '' };

            try {
                const res = await fetch(`${config.actorEndpoint}/${a.id}?format=html`);
                this.selectedActor.html = await res.text();
            } catch (e) {
                console.error('Erreur chargement détail acteur:', e);
                this.selectedActor.html = `<p class="text-center text-red-500 py-8">${config.i18n?.load_error || 'Load error'}</p>`;
            } finally {
                this.detailLoading = false;
            }
        },

        closeDetail() {
            this.detailOpen = false;
            this.selectedActor = null;
            this.$nextTick(() => this.invalidateMapSize());
        },

        reset() {
            this.search = '';
            this.type = '';
            this.country = '';
            this.reload();
        },
    };
};

window.geocoderMap = function geocoderMap(config) {
    return {
        map: null,
        marker: null,
        loading: false,
        message: '',
        messageType: 'success',
        address: config.initAddress || '',
        city: config.initCity || '',
        country: config.initCountry || '',
        lat: config.initLat,
        lng: config.initLng,
        _debounce: null,

        async init() {
            try {
                await waitForLeaflet();
                const startLat = this.lat ?? config.defaultLat;
                const startLng = this.lng ?? config.defaultLng;
                const startZoom = this.lat && this.lng ? 12 : config.defaultZoom;

                this.map = L.map(config.id).setView([startLat, startLng], startZoom);
                createOsmTileLayer().addTo(this.map);

                if (this.lat && this.lng) {
                    this.placeMarker(this.lat, this.lng, false);
                }

                this.map.on('click', (e) => {
                    this.placeMarker(e.latlng.lat, e.latlng.lng, true);
                });

                setTimeout(() => this.map?.invalidateSize(), 100);
            } catch (e) {
                console.error('Géocodeur:', e);
                this.message = 'Impossible de charger la carte.';
                this.messageType = 'error';
            }
        },

        async geocode() {
            if (!this.address || this.loading) return;
            this.loading = true;
            this.message = '';
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&limit=1&addressdetails=1&q=${encodeURIComponent(this.address)}`;
                const res = await fetch(url, {
                    headers: { 'Accept-Language': document.documentElement.lang || 'fr' },
                });
                if (!res.ok) throw new Error('Service indisponible');
                const data = await res.json();
                if (!data.length) {
                    this.message = 'Adresse introuvable. Placez le marqueur manuellement sur la carte.';
                    this.messageType = 'error';
                    return;
                }
                const r = data[0];
                this.placeMarker(parseFloat(r.lat), parseFloat(r.lon), true);
                this.map.setView([r.lat, r.lon], 12);
                if (r.address) {
                    this.city = r.address.city || r.address.town || r.address.village || r.address.municipality || this.city;
                    this.country = r.address.country || this.country;
                }
                this.message = `✓ ${r.display_name}`;
                this.messageType = 'success';
            } catch (e) {
                this.message = 'Erreur réseau. Placez le marqueur manuellement.';
                this.messageType = 'error';
            } finally {
                this.loading = false;
            }
        },

        placeMarker(lat, lng, animate = false) {
            this.lat = parseFloat(lat).toFixed(7);
            this.lng = parseFloat(lng).toFixed(7);
            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
            } else {
                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                this.marker.on('dragend', (e) => {
                    const p = e.target.getLatLng();
                    this.lat = p.lat.toFixed(7);
                    this.lng = p.lng.toFixed(7);
                });
            }
            if (animate) this.map.panTo([lat, lng]);
        },

        updateMarkerFromInputs() {
            clearTimeout(this._debounce);
            this._debounce = setTimeout(() => {
                if (this.lat && this.lng && this.map) {
                    this.placeMarker(this.lat, this.lng, true);
                }
            }, 500);
        },
    };
};
