<?php 

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

set_error_handler("ErrorHandler::errors");
set_exception_handler("ErrorHandler::exceptions");

header('Content-Type: application/json;charset=UTF-8');

$database   = new Database;

$request = json_decode(file_get_contents('php://input'), true);
$action   = (string)  $request['action'];
$id       = (int)     $request['id'];
$products = (string)  $request['products'];

switch ( $action ) {

    case 'showCartItems':
        
        break;

    case 'addToCart':

        $connection = $database->getConnection();
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