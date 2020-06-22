<?php 
    ob_start();
    session_start();
     $pageTitle="onlineShop.com"; 
    include "init.php";
?>  

    <!-- start  Loading-->
    <section id="loading">
        <div class="loading-content  d-flex align-items-center justify-content-center">
            <div class="lds-spinner">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </section>
    <!-- end  Loading-->

    <!-- start slider -->
    <header>

        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">

            <ol class="carousel-indicators mb-4">

                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>

                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>

                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>

            </ol>

            <div class="carousel-inner">
                <!-- Start Clothes Field  -->
                <div class="carousel-item active">
                    <img src="layout/images/bg1.jpg" class="d-block w-100" alt="...">

                    <div class="carousel-overlay d-flex align-items-center">
                        <div class="container">
                            <div class="slider-content text-white text-center">
                                <h1 class="">Get Awesome.</h1>
                                <p>New Fashion For You.</p>
                                <a href="#" class="btn btn-color">Show More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Clothes Field  -->

                <!-- Start HandMade Field  -->
                <div class="carousel-item">
                    <img src="layout/images/bg2.jpg" class="d-block w-100" alt="...">

                    <div class="carousel-overlay d-flex align-items-center">
                        <div class="container">
                            <div class="slider-content text-white text-center">
                                <h1 class="">Greate Hand Made.</h1>
                                <p>Shop Unique Hand Made Products.</p>
                                <a href="#" class="btn btn-color">Show More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End HandMade Field  -->

                <!-- Start Computers Field  -->
                <div class="carousel-item">
                    <img src="layout/images/bg3.jpg" class="d-block w-100" alt="...">

                    <div class="carousel-overlay d-flex align-items-center">
                        <div class="container">
                            <div class="slider-content text-white text-center">
                                <h1 class="">Great Electronic Devices.</h1>
                                <p>We help ambitious companies create new value.</p>
                                <a href="#" class="btn btn-color">Show More</a>
                            </div>
                        </div>
                    </div>
                </div>
               <!-- End Computers Field  -->

                <a class="control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <i class="fas fa-angle-double-left text-white"></i>
                    <span class="sr-only">Previous</span>
                    <div class="control-cover">
                        <i class="fas fa-angle-double-left "></i>
                    </div>
                </a>

                <a class=" control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <i class="fas fa-angle-double-right text-white"></i>
                    <span class="sr-only">Next</span>
                    <div class="control-cover">
                        <i class="fas fa-angle-double-right "></i>
                    </div>
                </a>
            </div>

        </div>

    </header>
    <!-- end slider -->

    
    <!-- Start Show All Items  -->
    <?php
        $allItems =getAllFrom('*', 'items','','',' WHERE Approve = 1', '', 'Item_ID');
        if(! empty($allItems))
        {
            echo "<div class='container-fluid pt-2'>";
            echo "<div class='public-bg'>";
                    echo "<div class='row'>";
                        foreach($allItems as $item)
                        {?>
                            <!-- Check If Category Has Items Hidden Or Not  -->
                            <?php if(checkStatus('Visibility','categories', 'ID',$item['Cat_ID']) == 0){?>

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
                                                <div class='clearfix'></div>
                                                <h3 class="card-title my-1"><a href="items.php?itemid=<?php echo $item['Item_ID'];?>"><?php echo $item['Name']; ?></a></h3>
                                                <p class="card-text"><?php echo $item['Description']; ?></p>
                                                <span class='readMore-btn badge badge-danger'>Read More</span>
                                            </div>
                                    </div>
                                </div>
                            <?php }?>
                        <?php }
                    echo "</div>";
                echo "</div>";
            echo "</div>";    
        }
                
    ?>
    <!-- End Show All Items  -->

<?php
    include $tpl."footer.php"; 
  ob_end_flush();
?>