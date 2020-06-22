<?php
if(isset($_POST["index"], $_POST["item_id"]))
{
    $connection = new PDO('mysql:host=localhost;dbname=shop', 'root', '');

    $stmt = $connection ->prepare("INSERT INTO item_rating(item_id, rating) VALUES (:item_id, :rating)");
    $stmt ->execute(array(
        'item_id'  => $_POST["item_id"],
        'rating'   => $_POST["index"]
    ));
    if($stmt)
    {
        echo "done";
    }
}

?>