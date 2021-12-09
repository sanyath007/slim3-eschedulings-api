<?php

$app->options('/{routes:.+}', function($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/', 'HomeController:home')->setName('home');

$app->post('/login', 'LoginController:login')->setName('login');

$app->group('/api', function(Slim\App $app) { 
    $app->get('/users', 'UserController:index');
    $app->get('/users/{loginname}', 'UserController:getUser');

    $app->get('/stats/{month}/patients', 'DashboardController:overallPatientStats');
    $app->get('/stats/{month}/beds', 'DashboardController:overallBedStats');
    $app->get('/stats/{month}/admit-day', 'DashboardController:admitDayStats');
    $app->get('/stats/{month}/collect-day', 'DashboardController:collectDayStats');

    $app->get('/wards', 'WardController:getAll');
    $app->get('/wards/{id}', 'WardController:getById');
    $app->post('/wards', 'WardController:store');
    $app->put('/wards/{id}', 'WardController:update');
    $app->delete('/wards/{id}', 'WardController:delete');
    $app->get('/wards/{ward}/beds', 'WardController:getWardBeds');
    $app->get('/wards/{id}/regises', 'WardController:getWardRegises');

    $app->get('/buildings', 'BuildingController:getAll');
    $app->get('/buildings/{id}', 'BuildingController:getById');
    $app->post('/buildings', 'BuildingController:store');
    $app->put('/buildings/{id}', 'BuildingController:update');
    $app->delete('/buildings/{id}', 'BuildingController:delete');    
    $app->get('/buildings/{id}/wards', 'BuildingController:getBuildingWards');

    $app->get('/schedulings', 'SchedulingController:getAll');
    $app->get('/schedulings/{id}', 'SchedulingController:getById');
    $app->get('/schedulings/add/init-form', 'SchedulingController:initForm');
    $app->get('/schedulings/member-of/depart/{depart}', 'SchedulingController:getMemberOfDepart');
    $app->get('/schedulings/member-of/division/{division}', 'SchedulingController:getMemberOfDivision');
    $app->post('/schedulings', 'SchedulingController:store');

    $app->get('/shifts', 'ShiftController:getAll');

    $app->get('/holidays', 'HolidayController:getAll');

    /** Routes to person db */
    $app->get('/departs', 'DepartController:getAll');
    $app->get('/departs/{id}', 'DepartController:getById');

    $app->get('/persons', 'PersonController:getAll');
    $app->get('/persons/{id}', 'PersonController:getById');
    $app->get('/persons/head-of/faction/{faction}', 'PersonController:getHeadOfFaction');
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
