<?php

namespace App;

require __DIR__.'/../vendor/autoload.php';

use function Stringy\create as s;

$repo = new Repository();

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

$app->get('/courses', function ($request, $response) use ($repo) {
    $params = [
        'courses' => $repo->all()
    ];
    //var_dump($repo); die;
    return $this->renderer->render($response, 'courses/index.phtml', $params);
});

// BEGIN (write your solution here)
$app->get('/courses/new', function ($req, $resp) {
    return $this->renderer->render($resp, 'courses/new.phtml');
});

$app->post('/courses', function ($req, $resp) use ($repo) {
    $course = $req->getParsedBodyParam('course');


    $validator = new Validator;
    $errors = $validator->validate($course);


    $params =  [
        'course' => $course,
        'errors' => $errors
    ];

    //var_dump($params); return;

    if (!empty($errors)) {
        return $this->renderer->render($resp, 'courses/new.phtml', $params);
    }

    $repo->save($course);

    //dump('$course сохранили!'); return;

    return $resp->withRedirect('/courses');
});
// END

$app->run();
