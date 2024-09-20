<?php
namespace App;
require_once 'Infrastructure/data_manager.php'; use DataManagers\DataManager;
require_once 'Infrastructure/Entities/data_mode_enum.php'; use DataSources\DataSource;

class Calculate
{
    private DataManager $data_manager;

    public function __construct($datasource = DataSource::DataBase)
    {
        $this->data_manager = new DataManager($datasource);
    }

    /*
    Method to calculate total price for rent
    */
    public function calculate1()
    {
        $days = !empty($_POST['days']) ? $_POST['days'] : 0;
        $product_id = isset($_POST['product']) ? $_POST['product'] : 0;
        $selected_services = isset($_POST['services']) ? $_POST['services'] : [];
        $product = $this->data_manager->select_product('a25_products', $product_id);
        if ($product) {
            $product = $product[0];
            $price = $product['PRICE'];
            $tarif = $product['TARIFF'];
        } else {
            echo "Ошибка, товар не найден!";
            return;
        }

        $tarifs = unserialize($tarif);
        if (is_array($tarifs)) {
            $product_price = $price;
            foreach ($tarifs as $day_count => $tarif_price) {
                if ($days >= $day_count) {
                    $product_price = $tarif_price;
                }
            }
            $total_price = $product_price * $days;
        }else{
            $total_price = $price * $days;
        }

        $services_price = 0;
        foreach ($selected_services as $service) {
            $services_price += (float)$service * $days;
        }

        $total_price += $services_price;

        echo $total_price;
    }
    /*
    Method to calculate price per day of rent
    */
    public function count_price() 
    {
        $days = !empty($_GET['days']) ? $_GET['days'] : 0;
        $product_id = isset($_GET['product']) ? $_GET['product'] : 0;
        $product = $this->data_manager->select_product('a25_products', $product_id);
        if ($product) {
            $product = $product[0];
            $price = $product['PRICE'];
            $tarif = $product['TARIFF'];
        } else {
            echo "Ошибка, товар не найден!";
            return;
        }

        $tarifs = unserialize($tarif);
        if (is_array($tarifs)) {
            $product_price = $price;
            foreach ($tarifs as $day_count => $tarif_price) {
                if ($days >= $day_count) {
                    $product_price = $tarif_price;
                }
            }
            $total_price = $product_price;
        }else{
            $total_price = $price;
        }
        echo $total_price;
    }
}

$instance = new Calculate();
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if($_POST['func_key'] === 'calculate1')
    {
        $instance->calculate1();
    }
}
if ($_SERVER['REQUEST_METHOD']==='GET')
{
    if ($_GET['func_key'] === 'count_price')
    {
        $instance->count_price();
    }
}