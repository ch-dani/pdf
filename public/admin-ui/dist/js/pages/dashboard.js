$(function () {
    'use strict';

    // World map by jvectormap
    $('#world-map').vectorMap({
        map: 'world_mill_en',
        backgroundColor: 'transparent',
        regionStyle: {
            initial: {
                fill: '#e4e4e4',
                'fill-opacity': 1,
                stroke: 'none',
                'stroke-width': 0,
                'stroke-opacity': 1
            }
        },
        series: {
            regions: [
                {
                    values: visitorsData,
                    scale: ['#92c1dc', '#ebf4f9'],
                    normalizeFunction: 'polynomial'
                }
            ]
        },
        onRegionLabelShow: function (e, el, code) {
            if (typeof visitorsData[code] != 'undefined')
                el.html(el.html() + ': ' + visitorsData[code] + ' visitors');
        },
    });

    // Sparkline charts
    var myvalues = [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021];
    $('#sparkline-1').sparkline(myvalues, {
        type: 'line',
        lineColor: '#92c1dc',
        fillColor: '#ebf4f9',
        height: '50',
        width: '80'
    });
    myvalues = [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921];
    $('#sparkline-2').sparkline(myvalues, {
        type: 'line',
        lineColor: '#92c1dc',
        fillColor: '#ebf4f9',
        height: '50',
        width: '80'
    });
    myvalues = [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21];
    $('#sparkline-3').sparkline(myvalues, {
        type: 'line',
        lineColor: '#92c1dc',
        fillColor: '#ebf4f9',
        height: '50',
        width: '80'
    });

    // The Calender
    /*$('#calendar').datepicker();*/

    // SLIMSCROLL FOR CHAT WIDGET
    $('#chat-box').slimScroll({
        height: '250px'
    });
    var dashboardPage = document.querySelectorAll("#revenue-chart") || false;

    if (dashboardPage) {
        /* Morris.js Charts */

        // Fix for charts under tabs
        $('.box ul.nav a').on('shown.bs.tab', function () {
            area.redraw();
            donut.redraw();
            line.redraw();
        });
    }

    /* The todo list plugin */
    $('.todo-list').todoList({
        onCheck: function () {
            window.console.log($(this), 'The element has been checked');
        },
        onUnCheck: function () {
            window.console.log($(this), 'The element has been unchecked');
        }
    });

});
