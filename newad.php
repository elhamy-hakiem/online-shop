<?php
    ob_start();
    session_start();
    $pageTitle="Create New Item"; 
    include "init.php";
    if(isset($_SESSION['user']))
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addItem']))
        {
            $formErrors = array();

            // Upload Variables 
            $itemImage  =  $_FILES['itemImage'];
            $imageName  =  $itemImage['name'];
            $imageType  =  $itemImage['type'];
            $imageTmp   =  $itemImage['tmp_name'];
            $imageSize  =  $itemImage['size'];
            $imageError =  $itemImage['error'];

            // List Of Allowed File Type To Upload 
            $imageAllowedExtension = array("jpeg","jpg","png","gif");

            //Get Image Extension
            $arrayName = explode(".",$imageName);
            $imageExtension = strtolower(end($arrayName));

            $name       = filter_var($_POST['name'],FILTER_SANITIZE_STRING) ; 
            $desc       = filter_var($_POST['description'],FILTER_SANITIZE_STRING) ;
            $price      = filter_var($_POST['price'],FILTER_SANITIZE_STRING) ;  
            $country    = filter_var($_POST['country'],FILTER_SANITIZE_STRING) ;
            $status     = filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT) ;  
            $category   = filter_var($_POST['category'],FILTER_SANITIZE_NUMBER_INT) ; 
            $tags       = filter_var($_POST['tags'],FILTER_SANITIZE_STRING); 

            if(empty($imageName))
            {
                $formErrors[] = "Image Is  <strong>Required</strong>";
            }
            if($imageError == 4)
            {
                $formErrors[] = "You Must Choose Image";
            }
            if(! empty($imageName) && ! in_array($imageExtension,$imageAllowedExtension))
            {
                $formErrors[] = "This Extension is Not <strong>Allowed</strong>";
            }
            if($imageSize > 3145728)
            {
                $formErrors[] = "Image Can't Be Larger Than <strong>3MB</strong>";
            }

            if(strlen($name) < 4)
            {
                $formErrors[] = "Name Must Be  Greater Than  <strong>4</strong> Chars";
            }
            if(empty($desc))
            {
                $formErrors[] = "Description Cant Be <strong>Empty</strong>";
            }
            if(strlen($desc) < 30)
            {
                $formErrors[] = "Description Cant Be Less Than <strong>50</strong> Characters";
            }
            if(strlen($desc) > 204)
            {
                $formErrors[] = "Description Cant Be Greater Than <strong>204</strong> Characters";
            }

            if(empty($price))
            {
                $formErrors[] = "Price Cant Be <strong>Empty</strong>";
            }
            
            if(strlen($country) < 2)
            {
                $formErrors[] = "Country Must Be  Greater Than <strong>2</strong> Chars";
            }
            if(empty($status))
            {
                $formErrors[] = "You Must Choose the <strong>Status</strong>";
            }
            if(empty($category))
            {
                $formErrors[] = "You Must Choose the <strong>Category</strong>";
            }

            if(empty($formErrors))
            {
                // Edit User Avatar Name Before Insert In Database 
                $image = rand(0,1000000000).'_'.$imageName;
                move_uploaded_file($imageTmp,"admin\uploads\products\\".$image);

                // Add New Item In Database 
                $stmt = $connection ->prepare("INSERT INTO `items`(`Name`, `Description`, `Price`, `Country_Made`, `Image`, `Status`, `Add_Date`, `Cat_ID`, `Member_ID`, `tags`) 
                VALUES (:zname, :zdesc, :zprice, :zcountry, :zitemImage, :zstatus , now(), :zcat, :zmember, :ztags )");
                    $stmt ->execute(array(
                        'zname'         => $name,
                        'zdesc'         => $desc,
                        'zprice'        => $price,
                        'zcountry'      => $country,
                        'zitemImage'    => $image,
                        'zstatus'       => $status,
                        'zcat'          => $category,
                        'zmember'       => $_SESSION['userid'],
                        'ztags'         => $tags, 
                    ));
                    if($stmt)
                    {
                        $success_msg = " Item Added ";
                    }
            }

        }
?>
    <div class="container">
        <!-- Start Create New Item -->
        <div class="page-header">
                <h1 class="text-center"><?php echo $pageTitle ; ?></h1>
        </div>

        <!-- Start Show Errors  -->
        <?php 
            if(! empty($formErrors))
            {
                echo '<div class="form-error alert alert-danger text-center">';
                    foreach($formErrors as $error)
                    {?>
                        <div class="error">
                            <?php echo $error; ?>
                        </div>
                    <?php }
                echo ' </div>';
                // refresh page 
                header("refresh:2");
            }
            if(isset($success_msg) && !empty($success_msg))
            {
                //Show Success Message
                    echo '<div class="form-error alert alert-success text-center">';  
                        echo '<div>'. $success_msg .'<strong>Waiting Approval</strong></div>'; 
                    echo ' </div>';
                    // refresh page 
                    header("refresh:2");
            }    
        ?>
        <!-- End Show Errors  -->

        <div class='public-bg pt-4'>
             <?php 
             
                if(checkStatus('RegStatus','users','Username',$_SESSION['user']) == 0)
                {
                    echo  "<div class='container'><div class='page-header'><div class='alert alert-danger text-center'>
                             Sorry You Can't Add Item Because Your Account <strong> Waiting Approval </strong>
                           </div></div></div>";
                }
                else{
             ?>
                    <div class='row'>
                        <!-- Start Add Form  -->
                        <div class="col-md-8 col-sm-6">
                            <form class="px-2 add-form"  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">

                                <!-- Start Image Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12">Image</label>
                                    <div class="col-lg-10 col-md-9 col-sm-12">
                                        <!-- Start Upload item Image  -->
                                        <div class='custom-upload'>
                                            <span>Choose Photo</span>
                                            <input type="file"  name="itemImage">
                                        </div>
                                        <!-- End Upload item Image  -->
                                    </div>
                                </div>
                                <!-- End Image Field  -->

                                <!-- Start Name Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12 col-form-label">Name</label>
                                    <div class="col-lg-10 col-md-9 col-sm-12">
                                        <input 
                                            pattern=".{4,}"
                                            title="This Field Require At Least 4 chars"
                                            type="text" class="form-control live" name="name" 
                                            autocomplete="off"  placeholder="Item Name" 
                                            data-class=".live-title" required
                                        >
                                    </div>
                                </div>
                                <!-- End Name Field  -->

                                <!-- Start Description Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12 ">Description</label>
                                    <div class="col-lg-10 col-md-9 col-sm-12">
                                        <textarea 
                                            class="form-control live" name="description" 
                                            cols="30" rows="5"  placeholder="Descripe Your Item...." data-class=".live-desc"
                                            required
                                        ></textarea>
                                    </div>
                                </div>
                                <!-- End Description Field  -->

                                <!-- Start Price Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12">Price</label>
                                    <div class="col-lg-10 col-md-9 col-sm-12">
                                        <input type="text" class="form-control live" name="price"  placeholder=" Item Price " data-class=".live-price" required>
                                    </div>
                                </div>
                                <!-- End Price Field  -->

                                <!-- Start Country Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12">Country</label>
                                    <div class="col-lg-10  col-md-9 col-sm-12">
                                        <input type="text" class="form-control" name="country" placeholder="Country of Made " required>
                                    </div>
                                </div>
                                <!-- End Country Field  -->

                                <!-- Start Status Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12">Status</label>
                                    <div class="col-lg-10  col-md-9 col-sm-12">
                                        <select class="form-control selectpicker" name="status" required>
                                            <option value="">....</option>
                                            <option value="1">New</option>
                                            <option value="2">Like New</option>
                                            <option value="3">Used</option>
                                            <option value="4">Old</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- End Status Field  -->

                                <!-- Start Category Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12">Category</label>
                                    <div class="col-lg-10  col-md-9 col-sm-12">
                                        <select  class="form-control selectpicker" name="category" required>
                                            <option value="">....</option>
                                            <?php
                                                $cats =  getAllFrom('*' , 'categories', '',  '', 'WHERE parent = 0 ', 'AND Allow_Ads = 0', 'ID');
                                                if(! empty($cats))
                                                {
                                                    foreach($cats as $cat)
                                                    {
                                                        $childCats =  getAllFrom('*' , 'categories', '',  '', 'WHERE parent = '.$cat['ID'].'', 'AND Allow_Ads = 0', 'ID');
                                                        if(empty($childCats))
                                                        {
                                                            echo " <option class='main-category' value ='".$cat['ID']."'>".$cat['Name']."</option>";
                                                        }
                                                        else
                                                        {
                                                            echo "<optgroup label='".$cat['Name']."'>";
                                                                foreach($childCats as $child)
                                                                {
                                                                    echo "<option class='child' value ='".$child['ID']."'>".$child['Name']."</option>";
                                                                }
                                                            echo "</optgroup>"; 
                                                        }
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- End Category Field  -->  

                                <!-- Start Tags Field  -->
                                <div class="form-group row">
                                    <label class="col-lg-2 col-md-3 col-sm-12">Tags</label>
                                    <div class="col-lg-10  col-md-9 col-sm-12">
                                        <input type="text" class="form-control" id="tokenfield" name="tags" autocomplete="off"  placeholder="Separate tags with comma ( , )">
                                    </div>
                                </div>
                                <!-- End Tags Field  -->

                                <div class="form-group row">
                                    <div class="col-sm-10">
                                        <button type="submit" name="addItem" class="btn btn-success btn-sm "><i class="fa fa-plus"></i> Add Item</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- End Add Form  -->

                        <!-- Start Show Item  -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card item-box">
                                <span class="price-tag live-price">$5</span>
                                <img src="admin/uploads/products/add.jpg" alt="..." class="img-thumbnail">
                                <div class="card-body">
                                    <h3 class="card-title live-title">Title</h3>
                                    <p class="card-text live-desc">Description</p>
                                </div>
                            </div>
                        </div>
                        <!-- End Show Item  -->

                    </div>
               <?php }?>
        </div>
        <!-- End Create New Item -->
    </div>

<?php 
    }
    else
    {
       header("location:login.php");
        exit();
    } 
    include $tpl."footer.php"; 
    ob_end_flush();
?>