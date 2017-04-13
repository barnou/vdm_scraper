<?php

namespace App\Controller;

Use \App\DaO\PostDao;

class PostController {
    protected $container;

// constructor receives container instance
    public function __construct($container) {
        $this->container = $container;
    }

    public function all($request, $response, $args) {
        // your code
        // to access items in the container... $this->container->get('');
        try {
            $res = PostDao::getPosts($request->getQueryParams());

            return $response->withJson($res);
        } catch(\InvalidArgumentException $e) {
            $error = ['code'=> 401, 'message'=>$e->getMessage()];
            return $response->withStatus(401)
                ->withJson($error);
        }
    }

    public function single($request, $response, $args) {
        
        $res = PostDao::getPost($args['id']);

        return $response->withJson($res);
    }
}
