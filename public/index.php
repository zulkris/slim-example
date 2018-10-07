<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

$users = Generator::generate(57);

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');


$app->get('/', function ($request, $response) {
    return $this->renderer->render($response, 'index.phtml');
});


$app->get('/users', function ($request, $response) use ($users) {

    $pageId = $request->getQueryParam('page', 1);
    $usersPerPage = $request->getQueryParam('per', 5);
    $pagesCount = ceil(count($users)/$usersPerPage);

    $userArr = array_slice($users, ($pageId - 1) * $usersPerPage, $usersPerPage);

    $params = ['pageId' => $pageId, 'userArr' => $userArr, 'pagesCount' => $pagesCount, 'users' => $users];

    return $this->renderer->render($response, 'users/index.phtml', $params);
});


$app->get('/users/{id}', function ($request, $response, $args) use ($users) {
    $id = $args['id'];
    $params = ['user' => $users[$id]];
    return $this->renderer->render($response, 'users/show.phtml', $params);
});

$app->run();
