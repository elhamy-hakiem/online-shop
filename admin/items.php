<?php
/*
====================================================================
==  Items Members Page
==  You can Add || Edite || Delete Items From Here
====================================================================
*/
ob_start();
session_start();
$pageTitle = 'Items';

if(isset($_SESSION['Username']))
{
    include "init.php";
    $action = isset($_GET['action']) ? $_GET['action'] : 'Manage';

// Start Manage Page
    if($action == 'Manage')
    {
        // Start Method Pagination 
        $limit = isset($_POST['limit-records']) ? $_POST['limit-records'] : 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
        $start = ($page - 1) * $limit;
        $getCount = $connection ->prepare("SELECT COUNT(Item_ID) AS itemsCount FROM items");
        $getCount ->execute();
        $itemsCount = $getCount ->fetch();
        $total = intval($itemsCount['itemsCount']);
        $pages = ceil($total / $limit);
        $prevPage = $page - 1;
        $nextPage = $page + 1;
        // End Method Pagination 

        //Fetch All Items From Database
        $stmt = $connection ->prepare("SELECT 
                                            items.*, 
                                            categories.Name AS category_name,
                                            users.Username
                                       FROM   
                                            items
                                        INNER JOIN 
                                            categories 
                                        ON
                                            categories.ID = items.Cat_ID
                                        INNER JOIN
                                            users
                                        ON
                                            users.UserID  = items.Member_ID
                                        ORDER BY
                                            Item_ID
                                        DESC
                                        LIMIT $start , $limit
                                     ");    
        $stmt ->execute();
        //Assign Items In Variable
        $items = $stmt ->fetchAll();
        //Check If Found Items Or No
        $count = $stmt ->rowCount();
    ?>
    <!-- Start Manage Page Design -->
    <div class="container manage-items">
        <div class="card my-4">
            <div class="card-body">
                <h1 class="edit-header text-center py-3">Manage Items</h1><hr>
                <!-- Start Choose Limit To Show  -->
                <div class='select-limit'>
                    <form method="POST" >
                        <span>show</span>
                        <select name="limit-records" id="limit-records">
                            <option disabled ='disabled' selected>limit</option>
                            <?php foreach([10,30,60,80] as $limit) { ?> 
                                <option <?php if( isset($_POST["limit-records"]) && $_POST["limit-records"] == $limit) {echo "selected";}?> value="<?php echo $limit; ?>">
                                    <?php echo $limit; ?>
                                </option>
                            <?php }?>
                        </select>
                        <span>Items</span>
                    </form>
                </div>
                <!-- End Choose Limit To Show  -->
                <div class="table-responsive">
                    <table class="table text-center table-bordered">
                        
                        <?php if($count > 0) {?>
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Adding Date</th>
                                <th>Category</th>
                                <th>Username</th>
                                <th>Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach($items as $item){
                                echo "<tr>";
                                    echo "<td>".$item['Item_ID']."</td>";
                                    echo "<td>";
                                        if(empty($item['Image']))
                                        {
                                            echo "<img class='item-img' src='uploads/products/add.jpg' alt ='Item Image'/>";
                                        }
                                        else
                                        {
                                            echo "<img class='item-img' src= 'uploads/products/".$item['Image']."' alt='Item Image'>";
                                        }
                                    echo "</td>";
                                    echo "<td>".$item['Name']."</td>";
                                    echo "<td class='desc-content'>".$item['Description']."</td>";
                                    echo "<td>".$item['Price']."</td>";
                                    echo "<td>".$item['Add_Date']."</td>";
                                    echo "<td>".$item['category_name']."</td>";
                                    echo "<td>".$item['Username']."</td>";
                                    echo "<td>
                                            <a href='items.php?action=Edit&itemid=".$item['Item_ID']."' class='btn btn-success edite-btn'><i class='fas fa-edit'></i> Edit</a>
                                            <button id='delete-btn' data-itemid='".$item['Item_ID']."' class='btn btn-danger delete-btn'><i class='fas fa-trash-alt'></i> Delete</button>";
                                            if($item['Approve'] == 0)
                                            {
                                                echo "<a href='items.php?action=Approve&itemid=".$item['Item_ID']."' class='btn btn-info ml-2 active-btn'><i class='fa fa-check'></i> Approve</a>";
                                            }
                                     echo "</td>";
                                echo "</tr>";
                            }?>
                        </tbody>
                    <?php 
                        }else{echo "<div class='alert alert-danger text-center font-weight-bold'>Not Found <span class='badge badge-pill badge-danger'> Items </span></div>";} ?>
                    </table>
                </div>
                <a href='items.php?action=Add' class="addItem-btn btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Item</a>
                <!-- Start pagination  -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination custom-pagination">
                            <li class="page-item  <?php if($prevPage < 1 ){echo "disabled";} ?>">
                                <a class="page-link" href="<?php echo "items.php?action=Manage&page=".$prevPage;?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>

                            <?php 
                                // Custom Pagination Number 
                                $customPageNum = 6;
                                if($pages <= 6)
                                {
                                    $customPageNum = $pages;
                                }
                                for ( $i =1 ; $i <= $customPageNum ; $i++ ){
                                    echo '<li class="page-item">';
                                        echo '<a class="page-link" href="items.php?action=Manage&page='.$i.'">';
                                            echo $i;
                                        echo'</a>';
                                    echo '</li>';
                                }
                            ?>
                            <li class="page-item <?php if($nextPage > $pages){echo "disabled";} ?>">
                                <a class="page-link" href="<?php echo "items.php?action=Manage&page=".$nextPage;?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span aria-hidden="true" class="sr-only">Next</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class='clearfix'></div>
                <!-- End pagination  -->
            </div>
        </div>
    </div>
   <!-- End Manage Page Design -->
<?php }
// End Manage Page

//Start Add Page 
 elseif($action == 'Add')
 {?>
     <!-- Start Add Page Design     -->
        <div class="container">
            <div class="card my-4">
                <div class="card-body pb-0">
                    <h1 class="edit-header text-center py-3">Add New Item</h1><hr>

                    <!-- Start Add Form  -->
                    <form class="p-3" action="?action=Insert" method="POST" enctype="multipart/form-data">
                        <!-- Start Name Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" autocomplete="off"  placeholder="Item Name" required>
                            </div>
                        </div>
                        <!-- End Name Field  -->

                       <!-- Start Image Field  -->
                       <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Image</label>
                            <div class="col-sm-10">
                                <!-- Start Upload item Image  -->
                                <div class='custom-upload'>
                                    <span>Choose Photo</span>
                                    <input type="file"  name="itemImage">
                                </div>
                                <!-- End Upload item Image  -->
                            </div>
                        </div>
                        <!-- End Image Field  -->

                        <!-- Start Description Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="description"  cols="30" rows="8"  placeholder="Descripe Your Item...." required></textarea>
                            </div>
                        </div>
                        <!-- End Description Field  -->

                         <!-- Start Price Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Price</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="price"  placeholder=" Item Price " required>
                            </div>
                        </div>
                        <!-- End Price Field  -->

                         <!-- Start Country Field  -->
                         <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Country</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="country" placeholder="Country of Made " required>
                            </div>
                        </div>
                        <!-- End Country Field  -->

                         <!-- Start Status Field  -->
                         <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <select class="form-control selectpicker" name="status" required>
                                    <option value="0">....</option>
                                    <option value="1">New</option>
                                    <option value="2">Like New</option>
                                    <option value="3">Used</option>
                                    <option value="4">Old</option>
                                </select>
                            </div>
                        </div>
                        <!-- End Status Field  -->

                         <!-- Start Member Field  -->
                         <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Member</label>
                            <div class="col-sm-10">
                                <select class="form-control selectpicker" name="member" required>
                                    <option value="0">....</option>
                                    <?php
                                        $users = getAllFrom('*' , 'users', '',  '', '', '', 'UserID');
                                        foreach($users as $user)
                                        {
                                            echo "<option value=".$user['UserID'].">".$user['Username']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- End Member Field  -->   
                        
                         <!-- Start Category Field  -->
                         <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Category</label>
                            <div class="col-sm-10">
                                <select  class="form-control selectpicker" name="category" required>
                                    <option value="0">....</option>
                                    <?php
                                        $cats =  getAllFrom('*' , 'categories', '',  '', 'WHERE parent = 0 ', '', 'ID');
                                        foreach($cats as $cat)
                                        {
                                                $childCats =  getAllFrom('*' , 'categories', '',  '', 'WHERE parent = '.$cat['ID'].'', '', 'ID');
                                                if(empty($childCats))
                                                {
                                                    echo " <option class='main-category' value ='".$cat['ID']."'>".$cat['Name']."</option>";
                                                }
                                                else
                                                {
                                                    echo "<optgroup  label='".$cat['Name']."'>";
                                                    foreach($childCats as $child)
                                                    {
                                                        echo " <option class='child' value ='".$child['ID']."'>".$child['Name']."</option>";
                                                    }
                                                    echo "</optgroup>"; 
                                                }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- End Category Field  -->  

                        <!-- Start Tags Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Tags</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="tokenfield" name="tags" autocomplete="off"  placeholder="Separate tags with comma ( , )">
                            </div>
                        </div>
                        <!-- End Tags Field  -->

                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" name="add-item" class="btn btn-success btn-sm "><i class="fa fa-plus"></i> Add Item</button>
                            </div>
                        </div>
                    </form>
                    <!-- End Add Form  -->

                </div>
            </div>
        </div>
    <!-- End Add Page Design     -->


 <?php }
//End Add Page

//Start Insert Page
 elseif($action == 'Insert')
 {

    echo " <div class='container'><div class='card my-5'><div class='card-body'>
        <h1 class='edit-header text-center py-3'>Insert Item</h1><hr>";
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add-item']))
        {
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

            $name              = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
            $description       = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $price             = $_POST['price'];
            $country           = filter_var($_POST['country'],FILTER_SANITIZE_STRING);
            $status            = filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
            $category            = filter_var($_POST['category'],FILTER_SANITIZE_NUMBER_INT);
            $member            = filter_var($_POST['member'],FILTER_SANITIZE_NUMBER_INT);
            $tags              = filter_var($_POST['tags'],FILTER_SANITIZE_STRING);

            //Validate The Form
            $formErrors =array();

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
            if(empty($name))
            {
                $formErrors[] = "Name Cant Be <strong>Empty</strong>";
            }
            if(empty($description))
            {
                $formErrors[] = "Description Cant Be <strong>Empty</strong>";
            }
            if(strlen($description) < 30)
            {
                $formErrors[] = "Description Cant Be Less Than <strong>50</strong> Characters";
            }
            if(strlen($description) > 204)
            {
                $formErrors[] = "Description Cant Be Greater Than <strong>204</strong> Characters";
            }
            if(empty($price))
            {
                $formErrors[] = "Price Cant Be <strong>Empty</strong>";
            }
            if(empty($country))
            {
                $formErrors[] = "Country Cant Be <strong>Empty</strong>";
            }
            if($status == 0)
            {
                $formErrors[] = "You Must Choose the <strong>Status</strong>";
            }
            if($member == 0)
            {
                $formErrors[] = "You Must Choose the <strong>Member</strong>";
            }
            if($category == 0)
            {
                $formErrors[] = "You Must Choose the <strong>Category</strong>";
            }

            // Loop Into Errors And Show Error 
            foreach($formErrors as $error)
            {
                echo "<div class='alert alert-danger text-center'>".$error."</div>";
            }
            

            if(empty($formErrors))
            {
                // Edit User Avatar Name Before Insert In Database 
                $image = rand(0,1000000000).'_'.$imageName;
                move_uploaded_file($imageTmp,"uploads\products\\".$image);

                //Add New Item In Database 
                $stmt = $connection ->prepare("INSERT INTO `items`(`Name`, `Description`, `Price`, `Country_Made`, `Image`, `Status`, `Add_Date`, `Approve`, `Cat_ID`, `Member_ID`, `tags`) 
                                            VALUES (:zname, :zdesc, :zprice, :zcountry, :zitemImage , :zstatus , now(), 1, :zcat, :zmember, :ztags)");
                $stmt ->execute(array(
                    'zname'         => $name,
                    'zdesc'         => $description,
                    'zprice'        => $price,
                    'zcountry'      => $country,
                    'zitemImage'    => $image,
                    'zstatus'       => $status,
                    'zcat'          => $category,
                    'zmember'       => $member,
                    'ztags'         =>$tags
                ));

                //Show Success Message
                $theMsg = "<div class='alert alert-success text-center'><span class='badge badge-pill badge-success'>". $stmt->rowCount() ."</span>  Item Added</div>";
                redirectHome($theMsg,'back');
            }
        }
        //Show Error Message
        else{

            $theMsg= "<div class='alert alert-danger text-center'><span class='badge badge-pill badge-danger py-2 px-3'>Sorry</span> You Cant Browse This Page Directory !</div>";
            redirectHome($theMsg);
        }
        echo "</div></div></div>";

 }
//End Insert Page

//Start Edite Page 
 elseif($action == 'Edit'){

    echo "<div class='container'>
    <div class='card mb-4 mt-5'>
        <div class='card-body'>
            <h1 class='edit-header text-center py-3'>Edit Item</h1><hr>";
    //Check if Get Request Is Numeric &Get value Of It
    $itemid =  isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

    //Select All Data Depend On This ID 
    $stmt = $connection ->prepare("SELECT * FROM items WHERE Item_ID = :itemid LIMIT 1");
    $stmt ->bindparam(":itemid",$itemid);
    $stmt ->execute();
    $item = $stmt ->fetch();
    $count = $stmt ->rowCount();
    if($count > 0 ){?>

        <!-- Start Edite Form  -->
        <form class="p-3" action="?action=Update" method="POST">
                    <input type="hidden" name="itemid" value="<?php echo $itemid ;?>">
                    <!-- Start Name Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" autocomplete="off"  placeholder="Item Name" value="<?php echo $item['Name'] ?>" required>
                        </div>
                    </div>
                    <!-- End Name Field  -->

                    <!-- Start Description Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description"  cols="30" rows="8"  
                                      placeholder="Descripe Your Item...." required><?php echo $item['Description'] ?></textarea>
                        </div>
                    </div>
                    <!-- End Description Field  -->

                    <!-- Start Price Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Price</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="price"  placeholder=" Item Price " value="<?php echo $item['Price'] ?>" required>
                        </div>
                    </div>
                    <!-- End Price Field  -->

                    <!-- Start Country Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Country</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="country" placeholder="Country of Made " value="<?php echo $item['Country_Made'] ?>" required>
                        </div>
                    </div>
                    <!-- End Country Field  -->

                    <!-- Start Status Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control selectpicker" name="status" required>
                                <option value="1" <?php if($item['Status'] == 1) {echo "selected" ;} ?>>New</option>
                                <option value="2" <?php if($item['Status'] == 2) {echo "selected" ;} ?>>Like New</option>
                                <option value="3" <?php if($item['Status'] == 3) {echo "selected" ;} ?>>Used</option>
                                <option value="4" <?php if($item['Status'] == 4) {echo "selected" ;} ?>>Old</option>
                            </select>
                        </div>
                    </div>
                    <!-- End Status Field  -->

                    <!-- Start Member Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Member</label>
                        <div class="col-sm-10">
                            <select class="form-control selectpicker" name="member" required>
                                <?php
                                    $users = getAllFrom('*' , 'users', '',  '', '', '', 'UserID');
                                    foreach($users as $user)
                                    {
                                        echo "<option value='".$user['UserID']."'";
                                            if($item['Member_ID'] == $user['UserID'] ){echo "selected" ;}
                                        echo ">".$user['Username']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- End Member Field  -->   
                    
                    <!-- Start Category Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">
                            <select  class="form-control selectpicker" name="category" required>
                                <?php
                                    $cats =  getAllFrom('*' , 'categories', '',  '', 'WHERE parent = 0 ', '', 'ID');
                                    foreach($cats as $cat)
                                    {
                                            $childCats =  getAllFrom('*' , 'categories', '',  '', 'WHERE parent = '.$cat['ID'].'', '', 'ID');
                                            if(empty($childCats))
                                            {
                                                echo " <option class='main-category' value ='".$cat['ID']."'";
                                                    if($item['Cat_ID'] == $cat['ID'] ){echo "selected" ;}
                                                echo ">".$cat['Name']."</option>";
                                            }
                                            else
                                            {
                                                echo "<optgroup  label='".$cat['Name']."'>";
                                                foreach($childCats as $child)
                                                {
                                                    echo " <option class='child' value ='".$child['ID']."'";
                                                         if($item['Cat_ID'] == $child['ID'] ){echo "selected" ;}
                                                    echo ">".$child['Name']."</option>";
                                                }
                                                echo "</optgroup>"; 
                                            }
                                    }
                                ?>
                             </select>
                        </div>
                    </div>
                    <!-- End Category Field  -->  
                    
                    <!-- Start Tags Field  -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tags</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $item['tags'];?>" class="form-control" id="tokenfield" name="tags" autocomplete="off"  placeholder="Separate tags with comma ( , )">
                        </div>
                    </div>
                    <!-- End Tags Field  -->

                    <div class="form-group row">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-success btn-sm "><i class="fas fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>
                <!-- End Edite Form  -->

    <?php 
    }else{
        $theMsg = "<div class='alert alert-danger text-center font-weight-bold'>This Item is Not Exist! </div>";
        redirectHome($theMsg,'back');
    }
    echo "</div></div>";
    
        // Start  Manage Comments of This Item 
            //Fetch All Comments From Database
            $stmt = $connection ->prepare("SELECT 
            comments.* , users.Username 
            FROM 
                comments 
            INNER JOIN
                users
            ON
                users.UserID = comments.user_id
            WHERE item_id = ?
            ORDER BY
                c_id
            DESC
            ");    
            $stmt ->execute(array($itemid));
            //Assign Comments In Variable
            $comments = $stmt ->fetchAll();
            //Check If Found Comments Or No
            $count = $stmt ->rowCount();
            ?>
            <!-- Start Manage Comments of This Item Page Design -->
            <div class="card mb-5">
                <div class="card-body">
                    <h1 class="second-header text-center py-3">Manage [ <?php echo $item['Name']; ?> ] Comments</h1><hr>
                    <div class="table-responsive">
                        <table class="table text-center table-bordered">
                            <?php if($count > 0) {?>
                            <thead class="thead-dark">
                                <tr>
                                    <th>Comments</th>
                                    <th>User Name</th>
                                    <th>Added Date</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  foreach($comments as $comment){
                                echo "<tr>";
                                    echo "<td>".$comment['comment']."</td>";
                                    echo "<td>".$comment['Username']."</td>";
                                    echo "<td>".$comment['comment_date']."</td>";
                                    echo "<td>
                                            <a href='comments.php?action=Edit&comid=".$comment['c_id']."' class='btn btn-success edite-btn'><i class='fa fa-edit'></i> Edit</a>
                                            <a href='comments.php?action=Delete&comid=".$comment['c_id']."' class='btn btn-danger delete-btn'><i class='fas fa-times'></i> Delete</a>";
                                            if($comment['status'] == 0)
                                            {
                                                echo "<a href='comments.php?action=Approve&comid=".$comment['c_id']."' class='btn btn-info ml-2 active-btn'><i class='fa fa-check'></i> Approve </a>";
                                            }
                                    echo "</td>";
                                echo "</tr>";
                                }?>
                            </tbody>
                            <?php 
                            }else{echo "<div class='alert alert-danger text-center font-weight-bold'>Not Found Comments</div>";} ?>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Manage Comments of This Item Page Design -->
   <?php echo "</div>";
 }
//End Edite Page
 
//Start Update Page
 elseif($action == 'Update'){ 
    
    echo " <div class='container'><div class='card my-5'><div class='card-body'>
    <h1 class='edit-header text-center py-3'>Update Item</h1><hr>";
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $id                = filter_var($_POST['itemid'],FILTER_SANITIZE_NUMBER_INT);
        $name              = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
        $description       = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
        $price             = $_POST['price'];

        $country           = filter_var($_POST['country'],FILTER_SANITIZE_STRING);
        $status            = filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
        $category          = filter_var($_POST['category'],FILTER_SANITIZE_NUMBER_INT);
        $member            = filter_var($_POST['member'],FILTER_SANITIZE_NUMBER_INT);
        $tags              = filter_var($_POST['tags'],FILTER_SANITIZE_STRING);

        //Validate The Form
        $formErrors =array();

        if(empty($name))
        {
            $formErrors[] = "Name Cant Be <strong>Empty</strong>";
        }
        if(empty($description))
        {
            $formErrors[] = "Description Cant Be <strong>Empty</strong>";
        }
        if(strlen($description) < 30)
        {
            $formErrors[] = "Description Cant Be Less Than <strong>50</strong> Characters";
        }
        if(strlen($description) > 204)
        {
            $formErrors[] = "Description Cant Be Greater Than <strong>204</strong> Characters";
        }
        if(empty($price))
        {
            $formErrors[] = "Price Cant Be <strong>Empty</strong>";
        }
        if(empty($country))
        {
            $formErrors[] = "Country Cant Be <strong>Empty</strong>";
        }
        if($status == 0)
        {
            $formErrors[] = "You Must Choose the <strong>Status</strong>";
        }
        if($member == 0)
        {
            $formErrors[] = "You Must Choose the <strong>Member</strong>";
        }
        if($category == 0)
        {
            $formErrors[] = "You Must Choose the <strong>Category</strong>";
        }

        // Loop Into Errors And Show Error 
        foreach($formErrors as $error)
        {
            echo "<div class='alert alert-danger text-center'>".$error."</div>";
        }
        

        if(empty($formErrors))
        {
                //Update Item In Database 
                $stmt = $connection ->prepare("UPDATE 
                                                    items 
                                                SET   
                                                    `Name`           = ?,
                                                    `Description`    = ?,
                                                    `Price`          = ?,
                                                    `Country_Made`   = ?,
                                                    `Status`         = ?,
                                                    `Cat_ID`         = ?,
                                                    `Member_ID`      = ?,
                                                    `tags`           = ?
                                                WHERE
                                                     Item_ID        = ? ");
                $stmt ->execute(array($name, $description, $price, $country, $status, $category, $member, $tags, $id));

                //Show Success Message
                $theMsg = "<div class='alert alert-success text-center'><span class='badge badge-pill badge-success py-2 px-3'>". $stmt->rowCount() ."</span>  Item Updated</div>";
                redirectHome($theMsg,'back');
        }

    }
    //Show Error Message
    else{

        $theMsg= "<div class='alert alert-danger text-center'><span class='badge badge-pill badge-danger py-2 px-3'>Sorry</span> You Cant Browse This Page Directory !</div>";
        redirectHome($theMsg);
    }
    echo "</div></div></div>";

 }
//End Update Page

//Start Delete Page
 elseif($action == 'Delete')
 {
  
    //Check if Get Request Is Numeric &Get value Of It
    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) :0;
        //Select All Data Depend On This ID 
        // replace CheckItem -> CountFunc 
        $check = countFunc('Item_ID','items',$itemid);
        if($check > 0 )
        {
            $stmt = $connection ->prepare("DELETE FROM items WHERE Item_ID = :itemid");
            $stmt ->bindParam(":itemid",$itemid);
            $stmt ->execute();
            $count = $stmt->rowCount();
            echo $count; 
        }
 }
// End Delete Page

//Start Approve Page
 elseif($action == 'Approve')
 {
    echo " <div class='container'>
            <div class='card mt-5'>
                <div class='card-body'><h1 class='edit-header text-center py-3'>Approve Item</h1><hr>";
                //Check if Get Request Is Numeric &Get value Of It
                $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']):0;

                //Select All Data Depend On This ID 
                // replace CheckItem -> CountFunc 
                $check = countFunc('Item_ID','items',$itemid);

                if($check > 0)
                {
                    $stmt = $connection ->prepare("UPDATE items SET Approve = 1 WHERE Item_ID = ? ");
                    $stmt ->execute(array($itemid));
                    $theMsg = "<div class='alert alert-success text-center'><span class='badge badge-pill badge-success'>". $stmt->rowCount() . "</span> Item Approved</div>";
                    redirectHome($theMsg,'back');
                }
                else
                {
                    $theMsg = "<div class='alert alert-danger text-center'>This Item Is Not Exist ! </div>";
                    redirectHome($theMsg);
                }
     echo "</div></div></div>";  
 }
// End Approve Page 

    else
    {
        $theMsg = "<div class='alert alert-danger text-center font-weight-bold mt-3'><span class='badge badge-pill badge-danger'>Sorry</span> This Page Is Not Found !</div>";
        redirectHome($theMsg);
    }

    include $tpl."footer.php";
}

else
{
    header("location: index.php");
    exit();
}

ob_end_flush();
?>