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
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$app->get('/', function ($request, $response) {
    return $this->renderer->render($response, 'index.phtml');
})->setName('root');

// show all users
$app->get('/posts', function ($request, $response) use ($repo) {
    $flash = $this->flash->getMessages();

    $params = [
        'flash' => $flash,
        'posts' => $repo->all()
    ];
    return $this->renderer->render($response, 'posts/index.phtml', $params);
})->setName('posts_show');


// BEGIN (write your solution here)

// show form to create new user
$app->get('/posts/new', function ($request, $response) {
    return $this->renderer->render($response, 'posts/new.phtml', []);
})->setName('create_new_post');
// add new user
$app->post('/posts', function ($request, $response) use ($repo) {
    $post = $request->getParsedBodyParam('post');
    $validator = new Validator();
    $errors = $validator->validate($post);
    if (count($errors) === 0) {
        $repo->save($post);
        $this->flash->addMessage('Test', 'Post has been created');

        $path = $this->router->pathFor('posts_show');
        return $response->withStatus(200)->withHeader('Location', $path);
    }
    $params = [
        'post' => $post,
        'errors' => $errors,
        'flash' => null
    ];
    //$path = $this->router->pathFor('create_new_post', $params);
    return $this->renderer->render($response, 'posts/new.phtml', $params);
});




// show current user
$app->get('/posts/{id}', function ($request, $response, $args) use ($repo) {
    $id = $args['id'];
    $post = $repo->find($id);
    if (!$post) {
        throw new Exception('oshibka', 500);
    }
    $flash = $this->flash->getMessages();
    $params = [
        'flash' => $flash,
        'post' => $post
    ];
    return $this->renderer->render($response, 'posts/edit.phtml', $params);
})->setName('post');

// edit current user
$app->put('/posts/{id}', function ($request, $response, $args) use ($repo) {

    $id = $args['id'];
    $post = $request->getParsedBodyParam('post');
    $post['id'] = $id;

    $validator = new Validator();
    $errors = $validator->validate($post);

    if (count($errors) === 0) {
        $repo->save($post);
        $this->flash->addMessage('Test', 'Post has been updated');
        $path = $this->router->pathFor('posts_show');
        return $response->withStatus(200)->withHeader('Location', $path);
    }
    $params = [
        'post' => $post,
        'errors' => $errors,
        'flash' => null
    ];
    return $this->renderer->render($response, 'posts/edit.phtml', $params);
})->setName('post_edit');


// delete current user
$app->delete('/posts/{id}', function ($request, $response, $args) use ($repo) {

    $id = $args['id'];
    $post = $repo->find($id);

    if (!empty($post)) {
        $repo->destroy($id);
        $this->flash->addMessage('test', 'Post has been deleted');
        $path = $this->router->pathFor('posts_show');
        return $response->withStatus(200)->withHeader('Location', $path);
    }
})->setName('post_delete');
// END

$app->run();
