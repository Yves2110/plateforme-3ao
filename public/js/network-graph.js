/**
 * Graphe réseau des acteurs (D3 force simulation).
 * Données : #graph-nodes-data et #graph-links-data (application/json).
 */
(function () {
    'use strict';

    function runWhenReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    function showEmpty(message) {
        var emptyEl = document.getElementById('network-empty');
        var graphEl = document.getElementById('network-graph');
        if (graphEl) graphEl.classList.add('hidden');
        if (emptyEl) {
            if (message) emptyEl.textContent = message;
            emptyEl.classList.remove('hidden');
        }
    }

    function initNetworkGraph() {
        if (typeof d3 === 'undefined') {
            console.error('Réseau: bibliothèque D3 non chargée');
            showEmpty('Impossible de charger la visualisation. Rechargez la page.');
            return;
        }

        var nodesEl = document.getElementById('graph-nodes-data');
        var linksEl = document.getElementById('graph-links-data');
        var graphEl = document.getElementById('network-graph');

        if (!nodesEl || !linksEl || !graphEl) {
            return;
        }

        var nodes, links;
        try {
            nodes = JSON.parse(nodesEl.textContent || '[]');
            links = JSON.parse(linksEl.textContent || '[]');
        } catch (e) {
            console.error('Réseau: JSON invalide', e);
            showEmpty('Données du réseau invalides.');
            return;
        }

        if (!nodes.length) {
            showEmpty();
            return;
        }

        var typeColors = {
            'ONG': '#2D6A4F',
            'Institution': '#D4A017',
            'Réseau': '#40916C',
            'Réseau OP': '#40916C',
            'OP': '#40916C',
            'Entreprise': '#52B788',
            'Gouvernement': '#1A1A2E',
        };

        var W = graphEl.clientWidth || graphEl.offsetWidth || 800;
        var H = 600;

        graphEl.innerHTML = '';

        var svg = d3.select('#network-graph').append('svg').attr('width', W).attr('height', H);
        var tooltip = document.getElementById('graph-tooltip');

        var sim = d3.forceSimulation(nodes)
            .force('link', d3.forceLink(links).id(function (d) { return d.id; }).distance(90))
            .force('charge', d3.forceManyBody().strength(-280))
            .force('center', d3.forceCenter(W / 2, H / 2))
            .force('collision', d3.forceCollide().radius(function (d) { return 14 + (d.links || 0) * 2; }));

        var link = svg.append('g').attr('class', 'links').selectAll('line').data(links).join('line')
            .attr('class', 'link')
            .attr('stroke-width', function (d) { return d.type === 'projet' ? 2.5 : 1.5; });

        var node = svg.append('g').attr('class', 'nodes').selectAll('g').data(nodes).join('g')
            .attr('class', 'node')
            .call(d3.drag()
                .on('start', function (event, d) {
                    if (!event.active) sim.alphaTarget(0.3).restart();
                    d.fx = d.x;
                    d.fy = d.y;
                })
                .on('drag', function (event, d) {
                    d.fx = event.x;
                    d.fy = event.y;
                })
                .on('end', function (event, d) {
                    if (!event.active) sim.alphaTarget(0);
                    d.fx = null;
                    d.fy = null;
                }));

        node.append('circle')
            .attr('r', function (d) { return 8 + Math.min((d.links || 0) * 2, 12); })
            .attr('fill', function (d) { return typeColors[d.type] || '#52B788'; })
            .on('mouseover', function (event, d) {
                if (!tooltip) return;
                tooltip.classList.remove('hidden');
                tooltip.innerHTML = '<strong>' + d.name + '</strong><br><span style="opacity:.7">' + (d.type || '') + ' · ' + (d.country || '') + '</span>';
            })
            .on('mousemove', function (event) {
                if (!tooltip) return;
                var rect = graphEl.getBoundingClientRect();
                tooltip.style.left = (event.clientX - rect.left + 12) + 'px';
                tooltip.style.top = (event.clientY - rect.top - 10) + 'px';
            })
            .on('mouseout', function () {
                if (tooltip) tooltip.classList.add('hidden');
            })
            .on('click', function (event, d) {
                if (d.url) window.location.href = d.url;
            });

        node.append('text')
            .attr('dy', function (d) { return -(12 + Math.min((d.links || 0) * 2, 12)); })
            .attr('text-anchor', 'middle')
            .text(function (d) { return d.name.length > 20 ? d.name.slice(0, 18) + '…' : d.name; });

        sim.on('tick', function () {
            link
                .attr('x1', function (d) { return d.source.x; })
                .attr('y1', function (d) { return d.source.y; })
                .attr('x2', function (d) { return d.target.x; })
                .attr('y2', function (d) { return d.target.y; });
            node.attr('transform', function (d) { return 'translate(' + d.x + ',' + d.y + ')'; });
        });

        window.addEventListener('resize', function () {
            var w = graphEl.clientWidth || 800;
            svg.attr('width', w);
            sim.force('center', d3.forceCenter(w / 2, H / 2));
            sim.alpha(0.3).restart();
        });
    }

    runWhenReady(initNetworkGraph);
})();
