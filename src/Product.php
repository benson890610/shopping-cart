<?php 

class Product extends Model {

    public $id;
    public $title;
    public $price;
    public $image_name;
    public $created_at;
    public $updated_at;

    public function fullImagePath() {

        return $_ENV['APP_URL'] . 
               DIRECTORY_SEPARATOR . 'public' . 
               DIRECTORY_SEPARATOR . 'images' . 
               DIRECTORY_SEPARATOR . $this->image_name;

    }

    public function formatedPrice($decimals = 2, $separator = '.', $thousandsSeparator = ',') {

        return number_format($this->price, $decimals, $separator, $thousandsSeparator);

    }

}