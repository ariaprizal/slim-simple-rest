<?php

use Api\service\ProductService;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    // get All product
    $app->get('/product/', function (Request $request, Response $respone ){
        $sql = "SELECT  * FROM products";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($result = $stmt->fetchAll()) {
            return $respone->withJson(['status' => 'success', 'data' => $result], 200);
        }
        return $respone->withJson(['status' => 'Empty', 'data' => ''], 404); 
    });

    // get product by id
    $app->get('/product/{id}', function(Request $request, Response $response, $args){
        $id = $args["id"];
        $sql = "SELECT * FROM products WHERE id = $id";

        $stmt = $this->db->prepare($sql);
        $stmt ->execute();
        if ($result =$stmt->fetchAll()) {
            return $response->withJson(['Status' => "success", 'data' => $result], 200);
        }
        return $response->withJson(['Status' => "Not Found", 'data' => ''], 404);       
    });

    // create product
    $app->post('/product', function (Request $request, Response $response) use ($container) {
        $reqBody = $request->getParsedBody();
        /** @var ProductService $service */
        $service = $container["ProductService"];
        $product = $service->createProduct($reqBody);
        return $response->withHeader("Content-Type", "application/json")->withStatus(201)->write(json_encode($product, JSON_PRETTY_PRINT));
    });

    // delete product by id
    $app->delete('/product/delete/{id}', function (Request $request, Response $response, $args){
        $id = $args['id'];
        $sql = "DELETE FROM products where id = $id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($stmt->fetchAll()){
            return $response->withJson(['status' => 'deleted', 'data'=>''], 200);
        }

        return $response->withJson(['status' => 'Not Found', 'data'=>''], 404);
    });
};
