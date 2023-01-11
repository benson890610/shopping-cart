const addBtn           = document.querySelectorAll(".addToCartBtn");
const cartItems        = document.querySelector("#cart-items");
const cartTotal        = document.querySelector("#totalCartProducts");
const errorContainer   = document.querySelector("#error-container");
const successContainer = document.querySelector("#success-container");

function removeProductFromCart(object) {
    
    let id = object.dataset.productId;
    let cookieArray = document.cookie.split(";");
    let products = cookieArray[0].split("=");
    products = products[1];

    const data = {};
    data.action   = "removeCartItem";
    data.id       = id;
    data.products = products;

    $.ajax({
        url: "http://shopping-cart.test/api/process.php",
        type: "POST",
        dataType: "JSON",
        data: JSON.stringify(data),
        success: function(response) {

            if ( response.products.length === 0 ) {
                document.cookie = 'products=; Max-Age=-99999999;';
                document.cookie = 'totalProducts=; Max-Age=-99999999';

                cartItems.style.opacity = 0;
                cartItems.classList.remove("cart-items-visible");

                cartTotal.textContent = response.total;

                successContainer.textContent = "Product removed";
                successContainer.style.right = "0px";

                setTimeout(function() {
                    successContainer.style.right = "-300px";
                }, 2000);
                return false;
            }

            cartItems.innerHTML = "";

            response.products.forEach(function(product){

                const productContainer = document.createElement("div");
                productContainer.className = "flex justify-start items-center space-x-6 mb-4";

                const image = new Image();
                image.className = "w-24 h-24";
                image.src = product.image_name;

                const ul = document.createElement("ul");
                const li1 = document.createElement("li");
                li1.className = "mb-2";
                li1.textContent = product.title;
                const li2 = document.createElement("li");
                li2.textContent = `$${product.price}`;

                ul.appendChild(li1);
                ul.appendChild(li2);

                const deleteBtn = document.createElement("button");
                deleteBtn.className = "bg-red-600 text-white py-2 px-4 text-center";
                deleteBtn.textContent = "Remove";
                deleteBtn.dataset.productId = product.id;
                deleteBtn.setAttribute("onclick", "removeProductFromCart(this)");

                const hr = document.createElement("hr");

                productContainer.appendChild(image);
                productContainer.appendChild(ul);
                productContainer.appendChild(deleteBtn);
                productContainer.appendChild(hr);

                cartItems.appendChild(productContainer);
                cartItems.style.opacity = 1;
                cartItems.classList.add("cart-items-visible");

            });

            let date = new Date();
            date.setTime(date.getTime() + 86400 * 1000);
            let UTCDate = date.toUTCString();

            document.cookie = `products=${response.encodedProducts};expires=${UTCDate};path=/`;
            document.cookie = `totalProducts=${response.total}`;

            cartTotal.textContent = response.total;

            successContainer.textContent = "Product removed";
            successContainer.style.right = "0px";

            setTimeout(function() {
                successContainer.style.right = "-300px";
            }, 2000);

        }
    });

}

function showCartItems() {

    if ( cartItems.classList.contains("cart-items-visible") ) {
        cartItems.classList.remove("cart-items-visible");
        cartItems.style.opacity = 0;
        cartItems.innerHTML = "";
        return false;
    }

    const cookieArray = document.cookie.split(";");
    const cookieArrayItems = cookieArray[0].split("=");
    const productsData = {};
    productsData.action   = "showCartItems";
    productsData.products = cookieArrayItems[1];

    $.ajax({
        url: "http://shopping-cart.test/api/process.php",
        type: "POST",
        dataType: "JSON",
        data: JSON.stringify(productsData),
        success: function(response) {
            
            if ( response.status === 'OK' ) {

                response.products.forEach(function(product) {

                    const productContainer = document.createElement("div");
                    productContainer.className = "flex justify-start items-center space-x-6 mb-4";

                    const image = new Image();
                    image.className = "w-24 h-24";
                    image.src = product.image_name;

                    const ul = document.createElement("ul");
                    const li1 = document.createElement("li");
                    li1.className = "mb-2";
                    li1.textContent = product.title;
                    const li2 = document.createElement("li");
                    li2.textContent = `$${product.price}`;

                    ul.appendChild(li1);
                    ul.appendChild(li2);

                    const deleteBtn = document.createElement("button");
                    deleteBtn.className = "bg-red-600 text-white py-2 px-4 text-center";
                    deleteBtn.textContent = "Remove";
                    deleteBtn.dataset.productId = product.id;
                    deleteBtn.setAttribute("onclick", "removeProductFromCart(this)");

                    const hr = document.createElement("hr");

                    productContainer.appendChild(image);
                    productContainer.appendChild(ul);
                    productContainer.appendChild(deleteBtn);
                    productContainer.appendChild(hr);

                    cartItems.appendChild(productContainer);
                    cartItems.style.opacity = 1;
                    cartItems.classList.add("cart-items-visible");

                });

            }

        }
    });


}

if ( document.cookie != "" ) {

    let cookieArr = document.cookie.split(";");

    cookieArr.forEach(function(cookie) {

        let tmp = cookie.split("=");

        if ( tmp[0].trim() === "totalProducts") {
            cartTotal.textContent = tmp[1];
        }

    });

}

addBtn.forEach(function(btn) {

    btn.addEventListener("click", function(event) {

        let id = event.target.nextElementSibling.value;
        let cookieArr;
        let products = "";

        if ( document.cookie != "" ) {

            cookieArr = document.cookie.split(";");
            cookieArr.forEach(function(cookie){

                let tmp = cookie.split("=");

                if ( tmp[0].trim() === "products" ) {
                    products = tmp[1];
                }

            });

        }

        const data  = {};
        data.id     = id;
        data.action = 'addToCart';
        data.products = products;

        $.ajax({
            url: 'http://shopping-cart.test/api/process.php',
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(data),
            async: false,
            crossDomain: false,
            success: function(response) {

                if ( response.status === 'ERROR' ) {

                    errorContainer.textContent = response.message;
                    errorContainer.style.right = 0;

                    setTimeout(function(){
                        errorContainer.style.right = "-300px";
                    }, 2000);

                    return false;
                }

                let date = new Date();
                date.setTime(date.getTime() + 86400 * 1000);
                let expires = date.toUTCString();

                document.cookie = `products=${response.products};expires=${expires};path=/`;
                document.cookie = `totalProducts=${response.totalProducts};expires={expires};path=/`;

                cartTotal.textContent = response.totalProducts;
                successContainer.textContent = "Product added";
                successContainer.style.right = 0;

                setTimeout(function() {

                    successContainer.style.right = "-300px";

                }, 2000);

            }
        });

    });

});