<?php 

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

set_error_handler("ErrorHandler::errors");
set_exception_handler("ErrorHandler::exceptions");

header('Content-Type: application/json;charset=UTF-8');

$database   = new Database;
$connection = $database->getConnection();

$request = json_decode(file_get_contents('php://input'), true);
$action   = (string)  $request['action'];
$id       = $request['id'] ?? null;
$products = (string)  $request['products'];

switch ( $action ) {

    case "removeCartItem":

        $decodedProducts = json_decode(base64_decode($products), true);
        $selectedProducts = array_filter($decodedProducts, function($product) use ($id) {
            return $product['id'] != $id;
        });
        $selectedProducts = array_merge($selectedProducts, []);
        $selectedProducts = array_map(function($product) use ($connection) {

            $sql = "SELECT image_name FROM products WHERE id = :id LIMIT 1";

            $statement = $connection->prepare($sql);
            $statement->bindValue('id', $product['id'], PDO::PARAM_INT);
            $statement->execute();

            $imageName = $statement->fetchColumn();

            $product['image_name'] = $_ENV['APP_URL'] . '/public/images/' . $imageName;

            return $product;

        }, $selectedProducts);



        $encodedProducts = count($selectedProducts) > 0 ? base64_encode(json_encode($selectedProducts)) : "";

        echo json_encode(['products' => $selectedProducts, 'encodedProducts' => $encodedProducts, 'total' => count($selectedProducts)]);

        break;

    case 'showCartItems':
        
        $decodedProducts = json_decode(base64_decode($products), true);
        $decodedProducts = array_map(function($product) use ($connection) {

            $statement = $connection->prepare("SELECT image_name FROM products WHERE id = :id");
            $statement->bindValue('id', $product['id'], PDO::PARAM_INT);
            $statement->execute();

            $product['image_name'] = $_ENV['APP_URL'] . '/public/images/' . $statement->fetch(PDO::FETCH_ASSOC)['image_name'];

            return $product;

        }, $decodedProducts);

        echo json_encode([
            'status'   => 'OK',
            'products' => $decodedProducts
        ]);

        break;

    case 'addToCart':

        $statement  = $connection->prepare("SELECT id, title, price FROM products WHERE id = :id LIMIT 1");
        $statement->bindValue('id', $id);
        $statement->execute();

        $row = $statement->fetchObject('Product');
        $product = array();
        $product['id']    = $row->id;
        $product['title'] = $row->title;
        $product['price'] = $row->price;

        if ( ! empty($products) ) {

            $decodedProducts = json_decode(base64_decode($products), true);

            if ( in_array($product['id'], array_column($decodedProducts, 'id')) ) {
                echo json_encode(['status' => 'ERROR', 'message' => 'Can\'t add same product to cart']);
                exit;
            } 

            $decodedProducts[] = $product;
            $encodedProductItems = base64_encode(json_encode($decodedProducts));

            echo json_encode(['status' => 'OK', 'products' => $encodedProductItems, 'totalProducts' => count($decodedProducts)]);

        } else {

            $productItems   = array();
            $productItems[] = $product;
            $encodedProductItems = base64_encode(json_encode($productItems));

            echo json_encode(['status' => 'OK', 'products' => $encodedProductItems, 'totalProducts' => 1]);

        }

        break;

}