const addBtn           = document.querySelectorAll('.addToCartBtn');
const cartTotal        = document.querySelector('#totalCartProducts');
const errorContainer   = document.querySelector("#error-container");
const successContainer = document.querySelector("#success-container");

function showCartItems() {

    console.log('Cart Items...');

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
                document.cookie = `totalProducts=${response.totalProducts}`;

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