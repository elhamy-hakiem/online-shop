<?php  

    ob_start();
    session_start();
    $pageTitle="categories";  
    include "init.php";

?>
    
    <div class="container pt-2">
        <?php if( isset($_GET['name']) && is_string($_GET['name']) ) {  $tag = $_GET['name'];?>
            <div class="page-header">
                <h1 class="text-center">
                    <?php 
                        echo $tag;
                    ?>
                </h1>
            </div>
            <!-- Start Show Items  -->
            <?php
                echo "<div class='public-bg'>";
                        $tagsItems = getAllFrom('*' , 'items', '', '', 'WHERE tags LIKE "%'.$tag.'%" ', 'AND Approve = 1', 'Item_ID');
                        if(! empty($tagsItems))
                        {
                            echo "<div class='row'>";
                                foreach($tagsItems as $item)
                                {?>
                                    
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="card item-box mt-2">
                                                <span class="price-tag"><?php echo $item['Price']; ?></span>
                                                <?php 
                                                    if(empty($item['Image']))
                                                    {
                                                        echo "<img class='img-thumbnail' src='admin/uploads/products/add.jpg' alt ='Item Image'/>";
                                                    }
                                                    else
                                                    {
                                                        echo "<img class='img-thumbnail' src= 'admin/uploads/products/".$item['Image']."' alt='Item Image'>";
                                                    }
                                                ?>
                                                <!-- Start Show Item  -->
                                                <div class="card-body pt-2">
                                                    <div <?php if(isset($_SESSION['user'])) { echo 'id="rating-list" data-itemid="'.$item["Item_ID"].'"';}?> class='item-rating float-left'>
                                                        <?php
                                                            $rate = getRate($item["Item_ID"]);
                                                            for ($count =1 ; $count <=5 ; $count++)
                                                            {
                                                                if($count <= $rate )
                                                                {
                                                                    echo '<span class="rating-color" data-index="'.$count.'"><i class="far fa-star"></i></span>';
                                                                }
                                                                else
                                                                {
                                                                    echo '<span data-index="'.$count.'"><i class="far fa-star"></i></span>';
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                    <div class="item-date"><?php echo $item['Add_Date']; ?></div>
                                                    <h3 class="card-title my-1"><a href="items.php?itemid=<?php echo $item['Item_ID'];?>"><?php echo $item['Name']; ?></a></h3>
                                                    <p class="card-text"><?php echo $item['Description']; ?></p>
                                                    <span class='readMore-btn badge badge-danger'>Read More</span>
                                                </div>
                                                <!-- End Shoe Item  -->
                                        </div>
                                    </div>

                            <?php }
                            echo "</div>";
                        }
                        else
                        {
                            echo "<div class='alert alert-danger text-center mb-0'>Ther\'s No Items To Show</div>";
                        } 
                echo "</div>";
            ?>
            <!-- End Show Items  -->

        <?php 
            }
            else
                { ?>
                    <div class="page-header">
                        <div class='alert alert-danger text-center mb-0'>Ther\'s No Tags To Show</div>
                    </div>
            <?php }
        ?>
    </div>
  

<?php 

    include $tpl."footer.php"; 
    ob_end_flush();
?>