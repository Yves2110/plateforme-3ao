/**
 * Carte interactive 3AO — Leaflet (chargé avant le contenu via @stack('before-livewire'))
 */
(function () {
    'use strict';

    const TYPE_COLORS = {
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

    function leafletReady() {
        return typeof window.L !== 'undefined' && typeof L.map === 'function';
    }

    function clusterReady() {
        return typeof L.markerClusterGroup === 'function';
    }

    function waitForLeaflet(maxAttempts) {
        maxAttempts = maxAttempts || 150;
        return new Promise(function (resolve, reject) {
            var attempts = 0;
            function tick() {
                if (leafletReady()) {
                    resolve(window.L);
                    return;
                }
                if (++attempts >= maxAttempts) {
                    reject(new Error('Leaflet non chargé'));
                    return;
                }
                setTimeout(tick, 40);
            }
            tick();
        });
    }

    function createOsmTileLayer() {
        return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        });
    }

    function createClusterGroup() {
        if (clusterReady()) {
            return L.markerClusterGroup({
                showCoverageOnHover: false,
                spiderfyOnMaxZoom: true,
                chunkedLoading: true,
                iconCreateFunction: function (cluster) {
                    return L.divIcon({
                        html: '<div><span>' + cluster.getChildCount() + '</span></div>',
                        className: 'marker-cluster marker-cluster-3ao',
                        iconSize: L.point(40, 40),
                    });
                },
            });
        }
        return L.layerGroup();
    }

    function addMarkersToCluster(cluster, layers) {
        if (typeof cluster.addLayers === 'function') {
            cluster.addLayers(layers);
        } else {
            layers.forEach(function (layer) {
                cluster.addLayer(layer);
            });
        }
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

            init: function () {
                var self = this;
                var start = function () {
                    waitForLeaflet()
                        .then(function () {
                            var mapEl = document.getElementById('map');
                            if (!mapEl) {
                                throw new Error('Élément #map introuvable');
                            }

                            self.map = L.map(mapEl, { zoomControl: true }).setView([12, -3], 5);
                            createOsmTileLayer().addTo(self.map);

                            self.cluster = createClusterGroup();
                            self.map.addLayer(self.cluster);

                            window.addEventListener('resize', function () {
                                self.invalidateMapSize();
                            });

                            requestAnimationFrame(function () {
                                requestAnimationFrame(function () {
                                    self.invalidateMapSize();
                                });
                            });

                            return self.reload();
                        })
                        .catch(function (e) {
                            console.error('Carte:', e);
                            if (!self.map) {
                                self.mapError = (config.i18n && config.i18n.map_error) || 'Impossible de charger la carte.';
                            }
                        });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', start);
                } else {
                    start();
                }
            },

            invalidateMapSize: function () {
                if (this.map) {
                    this.map.invalidateSize();
                }
            },

            makeIcon: function (type) {
                var color = TYPE_COLORS[type] || '#52B788';
                return L.divIcon({
                    className: '',
                    html: '<div style="width:28px;height:28px;background:' + color + ';border:3px solid white;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14],
                    popupAnchor: [0, -14],
                });
            },

            makePopup: function (a) {
                var color = TYPE_COLORS[a.type] || '#52B788';
                var logo = a.logo
                    ? '<img src="' + a.logo + '" alt="" style="width:40px;height:40px;border-radius:8px;object-fit:cover;flex-shrink:0">'
                    : '<div style="width:40px;height:40px;border-radius:8px;background:' + color + ';color:white;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px">' + a.name.substring(0, 2).toUpperCase() + '</div>';
                var viewLabel = (config.i18n && config.i18n.view_profile) || 'Voir la fiche complète →';
                return (
                    '<div style="min-width:220px;max-width:280px">' +
                    '<div style="display:flex;gap:10px;align-items:center;margin-bottom:8px">' + logo +
                    '<div style="min-width:0"><p style="font-weight:700;font-size:14px;color:#1A1A2E;margin:0">' + a.name + '</p>' +
                    '<p style="font-size:11px;color:#9CA3AF;margin:2px 0 0">' + a.country + (a.city ? ' · ' + a.city : '') + '</p></div></div>' +
                    '<span style="display:inline-block;background:' + color + '20;color:' + color + ';padding:2px 8px;border-radius:999px;font-weight:600;font-size:10px">' + a.type + '</span>' +
                    (a.excerpt ? '<p style="font-size:12px;color:#6B7280;margin:8px 0 0">' + a.excerpt + '</p>' : '') +
                    '<a href="' + a.url + '" style="display:inline-block;margin-top:10px;font-size:12px;color:#2D6A4F;font-weight:700;text-decoration:none">' + viewLabel + '</a></div>'
                );
            },

            reload: function () {
                var self = this;
                if (!self.cluster) {
                    return Promise.resolve();
                }
                self.loading = true;
                var params = new URLSearchParams();
                if (self.search) params.set('q', self.search);
                if (self.type) params.set('type', self.type);
                if (self.country) params.set('country', self.country);
                history.replaceState(null, '', location.pathname + (params.toString() ? '?' + params : ''));
                return fetch(config.endpoint + '?' + params, { headers: { Accept: 'application/json' } })
                    .then(function (res) {
                        if (!res.ok) {
                            throw new Error('HTTP ' + res.status);
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        self.actors = data;
                        self.refreshMarkers();
                    })
                    .catch(function (e) {
                        console.error('Erreur chargement acteurs:', e);
                    })
                    .finally(function () {
                        self.loading = false;
                    });
            },

            refreshMarkers: function () {
                if (!this.cluster) {
                    return;
                }
                if (typeof this.cluster.clearLayers === 'function') {
                    this.cluster.clearLayers();
                }
                this.markers = {};
                var layers = [];
                var self = this;
                this.actors.forEach(function (a) {
                    if (a.lat == null || a.lng == null) {
                        return;
                    }
                    var m = L.marker([a.lat, a.lng], { icon: self.makeIcon(a.type) })
                        .bindPopup(self.makePopup(a), { maxWidth: 300 });
                    self.markers[a.id] = m;
                    layers.push(m);
                });
                addMarkersToCluster(this.cluster, layers);
            },

            focus: function (a) {
                var self = this;
                this.selected = a.id;
                if (!this.map) {
                    return;
                }
                this.map.setView([a.lat, a.lng], 11, { animate: true });
                setTimeout(function () {
                    var m = self.markers[a.id];
                    if (!m) {
                        return;
                    }
                    if (self.cluster && typeof self.cluster.zoomToShowLayer === 'function') {
                        self.cluster.zoomToShowLayer(m, function () {
                            m.openPopup();
                        });
                    } else {
                        m.openPopup();
                    }
                }, 400);
            },

            openDetail: function (a) {
                var self = this;
                this.focus(a);
                this.detailOpen = true;
                this.detailLoading = true;
                this.selectedActor = { id: a.id, name: a.name, url: a.url, html: '' };
                fetch(config.actorEndpoint + '/' + a.id + '?format=html')
                    .then(function (res) {
                        return res.text();
                    })
                    .then(function (html) {
                        self.selectedActor.html = html;
                    })
                    .catch(function () {
                        self.selectedActor.html = '<p class="text-center text-red-500 py-8">' + ((config.i18n && config.i18n.load_error) || 'Erreur') + '</p>';
                    })
                    .finally(function () {
                        self.detailLoading = false;
                    });
            },

            closeDetail: function () {
                this.detailOpen = false;
                this.selectedActor = null;
                var self = this;
                setTimeout(function () {
                    self.invalidateMapSize();
                }, 100);
            },

            reset: function () {
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

            init: function () {
                var self = this;
                var start = function () {
                    waitForLeaflet()
                        .then(function () {
                            var startLat = self.lat != null && self.lat !== '' ? parseFloat(self.lat) : config.defaultLat;
                            var startLng = self.lng != null && self.lng !== '' ? parseFloat(self.lng) : config.defaultLng;
                            var startZoom = self.lat && self.lng ? 12 : config.defaultZoom;
                            self.map = L.map(config.id).setView([startLat, startLng], startZoom);
                            createOsmTileLayer().addTo(self.map);
                            if (self.lat && self.lng) {
                                self.placeMarker(self.lat, self.lng, false);
                            }
                            self.map.on('click', function (e) {
                                self.placeMarker(e.latlng.lat, e.latlng.lng, true);
                            });
                            setTimeout(function () {
                                self.map.invalidateSize();
                            }, 100);
                        })
                        .catch(function (e) {
                            console.error('Géocodeur:', e);
                            self.message = 'Impossible de charger la carte.';
                            self.messageType = 'error';
                        });
                };
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', start);
                } else {
                    start();
                }
            },

            geocode: function () {
                var self = this;
                if (!this.address || this.loading) {
                    return;
                }
                this.loading = true;
                this.message = '';
                var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&addressdetails=1&q=' + encodeURIComponent(this.address);
                fetch(url, { headers: { 'Accept-Language': document.documentElement.lang || 'fr' } })
                    .then(function (res) {
                        if (!res.ok) {
                            throw new Error('Service indisponible');
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        if (!data.length) {
                            self.message = 'Adresse introuvable. Placez le marqueur manuellement sur la carte.';
                            self.messageType = 'error';
                            return;
                        }
                        var r = data[0];
                        self.placeMarker(parseFloat(r.lat), parseFloat(r.lon), true);
                        self.map.setView([r.lat, r.lon], 12);
                        if (r.address) {
                            self.city = r.address.city || r.address.town || r.address.village || r.address.municipality || self.city;
                            self.country = r.address.country || self.country;
                        }
                        self.message = '✓ ' + r.display_name;
                        self.messageType = 'success';
                    })
                    .catch(function () {
                        self.message = 'Erreur réseau. Placez le marqueur manuellement.';
                        self.messageType = 'error';
                    })
                    .finally(function () {
                        self.loading = false;
                    });
            },

            placeMarker: function (lat, lng, animate) {
                this.lat = parseFloat(lat).toFixed(7);
                this.lng = parseFloat(lng).toFixed(7);
                if (this.marker) {
                    this.marker.setLatLng([lat, lng]);
                } else {
                    var self = this;
                    this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                    this.marker.on('dragend', function (e) {
                        var p = e.target.getLatLng();
                        self.lat = p.lat.toFixed(7);
                        self.lng = p.lng.toFixed(7);
                    });
                }
                if (animate) {
                    this.map.panTo([lat, lng]);
                }
            },

            updateMarkerFromInputs: function () {
                var self = this;
                clearTimeout(this._debounce);
                this._debounce = setTimeout(function () {
                    if (self.lat && self.lng && self.map) {
                        self.placeMarker(self.lat, self.lng, true);
                    }
                }, 500);
            },
        };
    };
})();
