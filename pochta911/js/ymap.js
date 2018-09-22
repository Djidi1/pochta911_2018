jQuery(function ($) {
    if ($('#map').length) {
        ymaps.ready(init);
    }
});
var myMap, poly_neva_1,poly_neva_2,poly_neva_3, poly_spb_kad;
var order_route = [];

// Нева
var poly_neva_1_var = {
    "type": "Polygon",
    "coordinates": [[[30.508322,59.843532],[30.495275,59.848542],[30.493731,59.8602],[30.484289,59.866761],[30.476908,59.869],[30.461287,59.871417],[30.448927,59.881945],[30.444292,59.895252],[30.436224,59.902496],[30.419229,59.910347],[30.402833,59.921368],[30.395909,59.929635],[30.398312,59.934976],[30.401746,59.938939],[30.404149,59.948037],[30.40535,59.954065],[30.400544,59.957508],[30.385094,59.959144],[30.37943,59.956217],[30.379344,59.956475],[30.372048,59.95329],[30.364838,59.951998],[30.356299,59.952383],[30.339304,59.953588],[30.322138,59.948594],[30.315615,59.945925],[30.30583,59.941111],[30.297076,59.938183],[30.287806,59.93534],[30.277678,59.933273],[30.271498,59.929568],[30.267893,59.924657],[30.265662,59.919572],[30.260397,59.917774],[30.245529,59.920357],[30.218406,59.928112],[30.205532,59.930008],[30.207935,59.919064],[30.260607,59.916149],[30.268668,59.920506],[30.274326,59.929687],[30.281272,59.932577],[30.28856,59.933744],[30.323713,59.946178],[30.345475,59.951031],[30.364797,59.950664],[30.372883,59.952253],[30.385796,59.957324],[30.39536,59.956111],[30.402522,59.95266],[30.399582,59.940142],[30.391643,59.929311],[30.396363,59.923413],[30.414558,59.910924],[30.434126,59.902061],[30.442364,59.893816],[30.447337,59.881808],[30.460372,59.869952],[30.476831,59.8679],[30.484342,59.86483],[30.491468,59.859468],[30.490958,59.851332],[30.493198,59.845335]]]
};
// Малая Нева
var poly_neva_2_var = {
    "type": "Polygon",
    "coordinates": [[[30.310546,59.945109],[30.293594,59.946277],[30.278059,59.950152],[30.272136,59.952262],[30.261751,59.956065],[30.252052,59.95826],[30.239263,59.95882],[30.232397,59.962435],[30.206905,59.961876],[30.208536,59.969019],[30.225702,59.96704],[30.241924,59.961252],[30.264025,59.957001],[30.274046,59.953585],[30.28441,59.950512],[30.293873,59.947165],[30.315599,59.946567]]]
};

// Большая Нева
var poly_neva_3_var = {
    "type": "Polygon",
    "coordinates": [[[30.339957,59.952684],[30.335966,59.95764],[30.335708,59.961815],[30.335107,59.965603],[30.331417,59.970422],[30.326181,59.976129],[30.319443,59.978817],[30.304809,59.981334],[30.291892,59.982323],[30.275026,59.980903],[30.266786,59.982065],[30.260091,59.982452],[30.245071,59.981764],[30.230394,59.979957],[30.218978,59.978107],[30.20413,59.976],[30.203615,59.977118],[30.237175,59.981204],[30.24962,59.982452],[30.254942,59.982624],[30.261794,59.982688],[30.267959,59.982237],[30.275341,59.981334],[30.28461,59.982839],[30.294137,59.983592],[30.304523,59.982452],[30.321088,59.979484],[30.328126,59.976086],[30.333362,59.971654],[30.336538,59.965803],[30.337217,59.960535],[30.340177,59.955237],[30.345883,59.951401]]]
};

var poly_spb_kad_var = {
    "type": "Polygon",
    "coordinates": [[[30.188008,59.984917],[30.192815,60.005382],[30.206204,60.013805],[30.224744,60.010539],[30.23161,60.013289],[30.221997,60.021195],[30.206891,60.027724],[30.211011,60.034768],[30.237446,60.029614],[30.248776,60.032363],[30.223714,60.05297],[30.250836,60.062754],[30.267316,60.061209],[30.304394,60.069961],[30.360699,60.089345],[30.375462,60.09106],[30.382672,60.082828],[30.386449,60.071677],[30.387479,60.065843],[30.392285,60.060351],[30.401211,60.055888],[30.411168,60.05297],[30.424557,60.050051],[30.43623,60.045415],[30.444127,60.036313],[30.452367,60.026522],[30.458546,60.020851],[30.470219,60.016211],[30.476399,60.011227],[30.480519,60.004522],[30.481206,59.996785],[30.488072,59.991453],[30.493565,59.987669],[30.504552,59.984745],[30.520344,59.982508],[30.54163,59.976658],[30.55399,59.971324],[30.557423,59.966332],[30.555707,59.957381],[30.54678,59.947049],[30.54266,59.936713],[30.538197,59.928787],[30.530301,59.918791],[30.528927,59.903446],[30.529614,59.896893],[30.527898,59.887404],[30.533734,59.876188],[30.534764,59.868247],[30.520001,59.85806],[30.511761,59.851152],[30.503178,59.84977],[30.486699,59.848733],[30.492879,59.84286],[30.504895,59.842169],[30.515538,59.838195],[30.520344,59.832665],[30.515195,59.827307],[30.486355,59.837849],[30.477772,59.850633],[30.46095,59.843205],[30.45271,59.835775],[30.443783,59.827653],[30.435887,59.823504],[30.394688,59.815897],[30.363446,59.81313],[30.35143,59.812265],[30.33289,59.807769],[30.316067,59.809498],[30.290729,59.824714],[30.274593,59.830764],[30.255367,59.829554],[30.234424,59.824195],[30.207301,59.821602],[30.138637,59.824023],[30.138637,59.834047],[30.125247,59.835084],[30.127994,59.849424],[30.129711,59.863455]]]
};


function init() {
    // Создаем карту с добавленными на нее кнопками.
    myMap = new ymaps.Map('map', {
        center: [30.328228,59.939784],
        zoom: 10,
        controls: []
        // controls: [trafficButton, viaPointButton]
    }, {
        buttonMaxWidth: 300
    });

/*
    // До Невы
    poly_neva_kad = new ymaps.Polygon(poly_neva_kad_var.coordinates, { hintContent: "Зона до невы"  }, {
        fillColor: '#00FF0050',
        strokeWidth: 3
    });
    poly_neva_kad.options.set('visible', true);
    myMap.geoObjects.add(poly_neva_kad);
*/


    poly_spb_kad = createPolyObjects(poly_spb_kad_var,'#00FF0050');
    poly_neva_1 = createPolyObjects(poly_neva_1_var,'#FF000050');
    poly_neva_2 = createPolyObjects(poly_neva_2_var,'#FF000050');
    poly_neva_3 = createPolyObjects(poly_neva_3_var,'#FF000050');






    var order_id = $('#order_id').val();
    if (order_id > 0) {
        calc_route();
    }


}
function calc_route() {
    $(".calc_route").prop('disabled', true);
    var route = [];
    // Начальная точка маршрута
    route.push($("SELECT.store_address option:selected").html());
    var i = 0;
    $('.spb-streets').each(function () {
        // Адреса доставки
        var route_address = $(this).val();
        var route_to_house = $(this).parent().find('.to_house').val();
        var route_to_corpus = $(this).parent().find('.to_corpus').val();
        if (route_address != '') {
            // route.push('Санкт-Петербург, ' + route_address + ((route_to_house != '')?(', '+route_to_house):'') + ((route_to_corpus != '')?(', '+route_to_corpus):''));
            route.push('' + route_address + ((route_to_house != '') ? (', ' + route_to_house) : '') + ((route_to_corpus != '') ? (', ' + route_to_corpus) : ''));
            i++;
        }
    });
    if (i == 0) {
        bootbox.alert('Необходимо ввести хотя бы один адрес доставки.');
        return false;
    }
    show_route(route);
}


function show_route(route_addresses) {
    LoadingMap(true);
    // Удаляем старые маршруты
    $.each(order_route, function () {
        this.removeFromMap(myMap);
    });

    ymaps.route(route_addresses, {
        // multiRoute: true,
        wayPointDraggable: true,
        mapStateAutoApply: true,
        avoidTrafficJams: false,
        results: 1
    }).then(function (route) {
        // Вывод результатов работы калькулятора
        var moveList = '';
        // Объединим в выборку все сегменты маршрута.
        var pathsObjects = ymaps.geoQuery(route.getPaths());

        var path_i = 0;
        pathsObjects.each(function (path) {
            // Факт пересечения невы
            var neva_cross = false;
            var edges = [];
            var coordinates = path.geometry.getCoordinates();
            for (var i = 1, l = coordinates.length; i < l; i++) {
                edges.push({
                    type: 'LineString',
                    coordinates: [coordinates[i], coordinates[i - 1]]

                });
            }

            var routeObjects = ymaps.geoQuery(edges)
                .add(route.getWayPoints())
                .add(route.getViaPoints())
                .setOptions('strokeWidth', 3)
                .setOptions('mapStateAutoApply', true)
                .setOptions('avoidTrafficJams', false);
            routeObjects.addToMap(myMap);
            // routeObjects.applyBoundsToMap(myMap);
            // Найдем все объекты, попадающие внутрь КАД.
            var objectsInSPb = routeObjects.searchInside(poly_spb_kad);
            // Найдем объекты, пересекающие КАД.
            var boundaryKADObjects = routeObjects.searchIntersect(poly_spb_kad);
            var boundaryNeva1Objects = routeObjects.searchIntersect(poly_neva_1);
            var boundaryNeva2Objects = routeObjects.searchIntersect(poly_neva_2);
            var boundaryNeva3Objects = routeObjects.searchIntersect(poly_neva_3);


            if (boundaryNeva1Objects.getLength() > 0
                || boundaryNeva2Objects.getLength() > 0
                || boundaryNeva3Objects.getLength() > 0
            ) {
                neva_cross = true;
            }

            // Раскрасим в разные цвета объекты внутри, снаружи и пересекающие КАД.
            boundaryKADObjects.setOptions({
                strokeColor: '#ffe708',
                preset: 'islands#yellowIcon'
            });
            objectsInSPb.setOptions({
                strokeColor: '#d300d6',
                preset: 'islands#greenIcon'
            });
            // Объекты за пределами КАД получим исключением полученных выборок из исходной.
            var objectsOutSideSPb = routeObjects.remove(objectsInSPb).remove(boundaryKADObjects).setOptions({
                strokeColor: '#ff000c',
                preset: 'islands#redIcon'
            });

            var outSideSPb = getPathDistance(objectsOutSideSPb);
            var inSideSPb = getPathDistance(objectsInSPb);

            var cost_km = 0;
            var cost_km_out = 0;
            var cost_Neva = 0;

            moveList += 'Участок №' + (+path_i+1) + ':';

            if (inSideSPb > 0) {
                cost_km = getRoutePrice(inSideSPb);
                moveList += ' по городу: ' + inSideSPb + 'км. (' + cost_km + ' р.);' + '<br/>';
            }
            if (neva_cross) {
                cost_Neva = $('input#km_neva').val();
                moveList += ' пересечение Невы: ' + cost_Neva + ' р.;' + '<br/>';
            }
            if (outSideSPb > 0) {
                cost_km_out = getOutKADprice(outSideSPb);
                moveList += ' за городом: ' + outSideSPb + 'км. (' + cost_km_out + ' р.) ' + '<br/>';
            }

            // Устанавливаем стоимость по маршруту и выполняем перерасчет
            var cost_route = $('.cost_route').eq(path_i).get();
            $(cost_route).val(parseFloat(cost_km)+parseFloat(cost_km_out)+parseFloat(cost_Neva));
            re_calc(cost_route);

            // Записываем маршрут в массив для очистки
            order_route.push(objectsInSPb);
            order_route.push(objectsOutSideSPb);
            order_route.push(boundaryKADObjects);

            path_i++;

        });

        // Выводим маршрутный лист.
        $('#viewContainer').html(moveList);
        LoadingMap(false);
    }, function (error) {
        alert('Возникла ошибка: ' + error.message);
    });
    $(".calc_route").prop('disabled', false);

}

function getPathDistance(objects){
    var dist = 0;
    objects.each(function(path) {
        var arrays_coord = path.geometry.getBounds();
        var diff = $(arrays_coord[0]).not(arrays_coord[1]).get();
        if (diff.length > 0) {
            dist += path.geometry.getDistance();
        }
    });
    return Math.ceil(dist / 1000);
}

function getRoutePrice(km_route){
    var cost_res = 0;
    $('input.km_cost').each(function(){
        var km_from = $(this).attr('km_from');
        var km_to = $(this).attr('km_to');
        var km_cost = $(this).val();
        if (km_route >= km_from && km_route < km_to ){
            cost_res = km_cost;
        }
    });
    return cost_res;
}

function getOutKADprice(dist){
    var cost_km_kad = $('input#km_kad').val();
    return dist*cost_km_kad;
}

function createPolyObjects(poly_var,color){
    var poly_data = new ymaps.Polygon(poly_var.coordinates, /*{ hintContent: "Санкт-Петербург"  },*/ {
        fillColor: color,
        strokeWidth: 3
    });
    poly_data.options.set('visible', true);
    myMap.geoObjects.add(poly_data);
    return poly_data;
}

function LoadingMap(show){
    if (show){
        $('div#map').addClass('loading');
    }else{
        $('div#map').removeClass('loading');
    }
}

function iLog(text) {
    console.log(text);
}
