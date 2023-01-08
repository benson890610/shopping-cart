<?php 

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

set_error_handler("ErrorHandler::errors");
set_exception_handler("ErrorHandler::exceptions");

$database  = new Database;
$conn      = $database->getConnection();
$statement = $conn->query("SELECT * FROM products ORDER BY id DESC");

$products = array();

while ( $row = $statement->fetchObject('Product') ) {
    $products[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Igor Djurdjic</title>
    <!-- Font Awesome Icons CDN -->
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" 
          integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer" />
    <!-- jQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" 
            integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" 
            crossorigin="anonymous" 
            referrerpolicy="no-referrer">
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clifford: '#da373d',
                    }
                }
            }
        }
  </script>
  <link rel="stylesheet" href="public/main.css">
</head>
<body>
    
    <!-- Error container -->
    <div
        id="error-container"
        class="fixed top-5 bg-purple-600 z-50 text-white text-center py-2 px-3" 
        style="width: 300px; transition: all .4s ease; right: -300px">
    </div>

    <!-- Success container -->
    <div
        id="success-container"
        class="fixed top-5 bg-green-500 z-50 text-white text-center py-2 px-3" 
        style="width: 300px; transition: all .4s ease; right: -300px">
    </div>

    <!-- Navigation bar -->
    <nav class="flex justify-between items-center py-5 px-10 bg-white">
        <div>
            <a href="<?php echo $_ENV['APP_URL'] ?>">
                <i class="fa-solid fa-code"></i>
                <span class="text-2xl">Home</span>
            </a>
        </div>
        <div>
            <span style="cursor: pointer" onclick="showCartItems()">
                <i class="fa-solid fa-cart-shopping"></i>
            </span>
            <span id="totalCartProducts" class="relative top-1 text-blue-700 font-bold text-lg">0</span>
        </div>
    </nav>

    <!-- Main header -->
    <header class="relative w-full h-96 bg-red-600 flex flex-col justify-center items-center text-center">
        <div class="bg-white absolute top-0 left-0 w-full h-full bg-no-repeat bg-center opacity-10" style="background-image: url(public/images/no-image.png)"></div>
        <h1 class="text-3xl font-bold text-white">Shopping Cart</h1>
    </header>

    <main class="mx-auto w-8/12 py-10 mb-16" style="display: flex; justify-content: space-between; flex-wrap:wrap; gap: 20px">
        
        <!-- All products -->
        <?php foreach($products as $index => $product): ?>

            <div class="w-3/12 border border-gray-200 py-5" style="background: linear-gradient(to top right, #00FFFF, #6495ED); box-shadow: 1px 2px 5px 2px gray">
                <div><img src="<?php echo $product->fullImagePath() ?>" alt=""></div>
                <h3 class="text-lg text-center mt-4"><?php echo $product->title ?></h3>
                <p class="text-gray-500 font-bold text-center mt-4 mb-4">$<?php echo $product->formatedPrice() ?></p>
                <button class="addToCartBtn bg-yellow-500 px-4 py-3 block mx-auto">Add To Cart</button>
                <input type="hidden" value="<?php echo $product->id ?>">
            </div>

        <?php endforeach ?>

    </main>

    <footer class="w-full py-5 text-center text-lg bg-sky-700 fixed bottom-0 left-0 mt-10 text-white">
        All Rights Reserved &copy; Igor Djurdjic
    </footer>
    
    <script src="<?php echo $_ENV['APP_URL'] . '/public/main.js' ?>"></script>
</body>
</html>