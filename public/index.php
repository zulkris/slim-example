<?php

namespace App;

require __DIR__.'/../vendor/autoload.php';

use function Stringy\create as s;
use Illuminate\Support\Collection ;

$users = Generator::generate(100);

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

// BEGIN (write your solution here)
$app->get('/users', function ($req, $resp, $args) use ($users) {
    $term = $req->getQueryParam('term'. '');

    $uzveri = collect($users)->filter(function ($value, $key) use ($term) {
        return s($value['firstName'])->startsWith($term, false);
    })->all();

    return $this->renderer->render($resp, 'users/index.phtml', ['term' => $term, 'users' => $uzveri]);
});
// END

$app->run();
