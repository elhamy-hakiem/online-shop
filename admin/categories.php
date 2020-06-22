<?php
/*
====================================================================
==  Manage Members Page
==  You can Add || Edite || Delete Members From Here
====================================================================
*/
ob_start();

session_start();
$pageTitle = 'Categories';

if(isset($_SESSION['Username']) )
{
    include "init.php";

    $action = isset($_GET['action']) ? $_GET['action'] : 'Manage';
//Start Manage Page
    if($action == 'Manage')
    {
        // Start Method Pagination 
        $limit = isset($_POST['limit-records']) ? $_POST['limit-records'] : 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
        $start = ($page - 1) * $limit;
        $getCount = $connection ->prepare("SELECT COUNT(ID) AS catsCount FROM categories");
        $getCount ->execute();
        $catsCount = $getCount ->fetch();
        $total = intval($catsCount['catsCount']);
        $pages = ceil($total / $limit);
        $prevPage = $page - 1;
        $nextPage = $page + 1;
        // End Method Pagination 

        $sort = 'ASC';
        $sort_array = array("ASC","DESC");
        if(isset($_GET['sort']) && in_array($_GET['sort'],$sort_array))
        {
            $sort = $_GET['sort'];
        }

        $stmt = $connection ->prepare("SELECT * FROM categories WHERE parent = 0 ORDER BY Ordering $sort LIMIT $start , $limit");
        $stmt ->execute();
        $cats = $stmt ->fetchAll();
        //Check If Found Categories Or No
        $count = $stmt ->rowCount();
     
        ?>
        <!-- Start Manage Page Design -->
        <div class="container categories">
            <div class="card my-4 px-4 pb-4">
                <div class="card-body">
                    <h1 class="edit-header text-center pb-3 pt-4">Manage Categories</h1><hr>
                </div>
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
                        <span>Categories</span>
                    </form>
                </div>
                <!-- End Choose Limit To Show  -->
                <div class="card-header">
                    Manage Categories
                    <div class="option float-right">
                        <i class="fa fa-sort"></i> Ordering : [
                        <a  class="<?php if($sort == 'ASC'){echo 'active';} ?>" href="?sort=ASC">Asc</a> |
                        <a  class="<?php if($sort == 'DESC'){echo 'active';} ?>" href="?sort=DESC">Desc</a> ] 
                        <i class="fa fa-eye"></i> View : [
                        <span class="active" data-view="full">Full</span> | 
                        <span>Classic</span> ]
                    </div>
                </div>
                <div class="card-body cat-body">
                    <?php if($count > 0) {?>
                            <?php  foreach($cats as $cat){
                                echo "<div class='cat'>";
                                    echo "<div class='hidden-buttons'>";
                                        echo "<a href='categories.php?action=Edit&catid=".$cat['ID']."' class='btn btn-success edite-btn'><i class='fa fa-edit'></i> Edit</a>";
                                        echo "<a href='categories.php?action=Delete&catid=".$cat['ID']."' class='btn btn-danger delete-btn'><i class='fa fa-trash'></i> Delete</a>";
                                    echo "</div>";
                                    echo "<h3>".$cat['Name']."</h3>";
                                    echo "<div class ='full-view'>";
                                            echo "<p>";if($cat['Description'] == ''){echo "This Category has No description ";}else{echo $cat['Description'];} echo"</p>";
                                            // Start Show Sub Categories 
                                            $subcats = getAllFrom('*', 'categories','', '', 'WHERE parent = '.$cat["ID"].'', '', 'ID');
                                            if(! empty($subcats))
                                            {
                                                echo "<h5 class='sub-head'><i class='fas fa-list-alt'></i> Child Category</h5>";
                                                echo "<ul class='list-unstyled sub-cats'>";
                                                foreach($subcats as $sub)
                                                {
                                                    echo "<li>";
                                                            echo"<a href='categories.php?action=Edit&catid=".$sub['ID']."'>";
                                                                echo $sub['Name'];
                                                                if($sub['Visibility'] == 1)   { echo "<i class = 'fas fa-eye-slash'></i>"; }
                                                                if($sub['Allow_Ads'] == 1)    { echo "<i class = 'fas fa-times'></i>"; }
                                                                if($sub['Allow_Comment'] == 1){ echo "<i class='fas fa-comment-slash'></i>"; }
                                                            echo "</a>";
                                                        echo "</li>";
                                                }
                                                echo "</ul>";
                                            }
                                            // End Show Sub Categories 
                                            if($cat['Visibility'] == 1)   { echo "<span class ='visibility global-span'><i class = 'fas fa-eye-slash'></i> Hidden</span>"; }
                                            if($cat['Allow_Ads'] == 1)    { echo "<span class ='advertises global-span'><i class = 'fas fa-times'></i> Ads Disabled</span>"; }
                                            if($cat['Allow_Comment'] == 1){ echo "<span class ='commenting global-span'><i class='fas fa-comment-slash'></i> Comment Disabled</span>"; }
                                    echo "</div>";
                                echo "</div>";
                                echo "<hr>";
                            }?>
                    <?php 
                        }else{echo "<div class='alert alert-danger text-center font-weight-bold mt-3 mx-3'>Not Found Categories</div>";} ?>
                </div>
                <a href='categories.php?action=Add' class=" add-category btn btn-primary"><i class="fa fa-plus"></i> New Category</a>
                <!-- Start pagination  -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination custom-pagination">
                            <li class="page-item  <?php if($prevPage < 1 ){echo "disabled";} ?>">
                                <a class="page-link" href="<?php echo "categories.php?action=Manage&page=".$prevPage;?>" aria-label="Previous">
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
                                        echo '<a class="page-link" href="categories.php?action=Manage&page='.$i.'">';
                                            echo $i;
                                        echo'</a>';
                                    echo '</li>';
                                }
                            ?>
                            <li class="page-item <?php if($nextPage > $pages){echo "disabled";} ?>">
                                <a class="page-link" href="<?php echo "categories.php?action=Manage&page=".$nextPage;?>" aria-label="Next">
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
       <!-- End Manage Page Design -->
    <?php
    }
//End Manage Page

//Start Add Page 
    elseif($action == 'Add')
    {?>
        
        <!-- Start Add Page Design     -->
        <div class="container">
            <div class="card my-4">
                <div class="card-body pb-0">
                    <h1 class="edit-header text-center py-3">Add New Category</h1><hr>

                    <!-- Start Add Form  -->
                    <form class="p-3" action="?action=Insert" method="POST">
                        <!-- Start Name Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" autocomplete="off" required="required" placeholder="Category Name">
                            </div>
                        </div>
                        <!-- End Name Field  -->

                        <!-- Start Description Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="description"  cols="30" rows="8" placeholder="Descripe Your Category...."></textarea>
                            </div>
                        </div>
                        <!-- End Description Field  -->

                         <!-- Start Ordering Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Ordering</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="ordering" placeholder=" Number To Arrange the Categories ">
                            </div>
                        </div>
                        <!-- End Ordering Field  -->

                        <!-- Start Category Type Field  -->
                        <div class="form-group row">
                            <label class="col-lg-2 col-md-3 col-sm-12">Parent ? </label>
                            <div class="col-lg-10  col-md-9 col-sm-12">
                                <select class="form-control selectpicker" name="parent" required>
                                    <option value="0">None</option>
                                    <?php
                                        $cats = getAllFrom('*', 'categories','', '', 'WHERE parent = 0', '', 'ID');
                                        foreach($cats as $cat)
                                        {
                                            // Check if Parent Have Items or not  
                                            $parentItems = getAllFrom('*' , 'items', '',  '', 'WHERE Cat_ID = '.$cat['ID'].'', '', 'Item_ID');
                                            if(empty($parentItems))
                                            {
                                                echo "<option value=".$cat['ID'].">".$cat['Name']."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- End Category Type Field  -->  

                        <!-- Start Visibility Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Visible</label>
                            <div class="col-sm-2 pt-2">
                                <input type="radio"  id="vis-yes" name="visibility" value="0" checked >
                                <label for="vis-yes">Yes</label>
                            </div>
                            <div class="col-sm-8 pt-2">
                                <input type="radio"  id="vis-no" name="visibility" value="1" >
                                <label for="vis-no">No</label>
                            </div>
                        </div>
                        <!-- End Visibility Field  -->

                        <!-- Start Ads Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Allow Ads</label>
                            <div class="col-sm-2 pt-2">
                                <input type="radio"  id="ads-yes" name="ads" value="0" checked >
                                <label for="ads-yes">Yes</label>
                            </div>
                            <div class="col-sm-8 pt-2">
                                <input type="radio"  id="ads-no" name="ads" value="1" >
                                <label for="ads-no">No</label>
                            </div>
                        </div>
                        <!-- End Ads Field  -->

                        <!-- Start Commenting Field  -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Allow Commenting</label>
                            <div class="col-sm-2 pt-2">
                                <input type="radio"  id="com-yes" name="commenting" value="0" checked >
                                <label for="com-yes">Yes</label>
                            </div>
                            <div class="col-sm-8 pt-2">
                                <input type="radio"  id="com-no" name="commenting" value="1" >
                                <label for="com-no">No</label>
                            </div>
                        </div>
                        <!-- End Commenting Field  -->

                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-success btn-sm"><i class='fa fa-plus'></i>  Add Category</button>
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
        <h1 class='edit-header text-center py-3'>Insert Member</h1><hr>";
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $name       = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
            $desc       = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $order      = $_POST['ordering'];
            $parent     = $_POST['parent'];
            $visible    = $_POST['visibility'];
            $ads        = $_POST['ads'];
            $comment    = $_POST['commenting'];


            if(!empty($name))
            {
                // check If Category Name Already Exist In Database Or No
                $count =countFunc('Name','categories',$name);
                if($count > 0 ){
                    $theMsg = "<div class='alert alert-danger text-center'>Sorry Username  Already Exist ! </div>"; 
                    redirectHome($theMsg,'back');
                }
                else
                {
                    //Add New Category In Database 
                    $stmt = $connection ->prepare("INSERT INTO `categories`(`Name`, `Description`, `Ordering`, `parent`, `Visibility`, `Allow_Ads`, `Allow_Comment`) 
                                                    VALUES (:zname, :zdesc, :zorder, :zparent, :zvisible, :zads, :zcomment)");
                    $stmt ->execute(array(
                        'zname'      => $name,
                        'zdesc'      => $desc,
                        'zorder'     => $order,
                        'zparent'    => $parent,
                        'zvisible'   => $visible,
                        'zads'       => $ads,
                        'zcomment'   => $comment,
                    ));

                    // //Show Success Message
                    $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Category Added</div>";
                    redirectHome($theMsg,'back');
                }
            }
            else
            {
                $theMsg= "<div class='alert alert-danger text-center'>You Must Type <strong>Category</strong> Name !</div>";
                redirectHome($theMsg,'back');   
            }

        }
        //Show Error Message
        else{

            $theMsg= "<div class='alert alert-danger text-center'>Sorry You Cant Browse This Page Directory !</div>";
            redirectHome($theMsg);
        }
        echo "</div></div></div>";
    }
//End Insert Page

//Start Edite Page 
    elseif($action == 'Edit'){

        echo "<div class='container'>
                <div class='card mt-5 mb-4'>
                    <div class='card-body'>
                        <h1 class='edit-header text-center py-3'>Edit Ctaegory</h1><hr>";
                        //Check if Get Request catid Is Numeric &Get value Of It
                        $catid = isset($_GET['catid'])&& is_numeric($_GET['catid']) ? intval($_GET['catid']) :0;

                        //Select All Data Depend On This ID 
                        $stmt = $connection -> prepare("SELECT * FROM categories Where ID = ? LIMIT 1");
                        $stmt ->execute(array($catid));
                        $cat  = $stmt -> fetch();
                        $count = $stmt ->rowCount();
                        if($count > 0 ){?>

                        <!-- Start Edit Form  -->
                        <form class="p-3" action="?action=Update" method="POST">
                            <input type="hidden" name="catid" value="<?php echo $catid ;?>">
                            <!-- Start Name Field  -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo $cat['Name']?>" name="name"  placeholder="Category Name">
                                </div>
                            </div>
                            <!-- End Name Field  -->

                            <!-- Start Description Field  -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="description"  cols="30" rows="8" placeholder="Descripe Your Category...."><?php echo $cat['Description']?></textarea>
                                </div>
                            </div>
                            <!-- End Description Field  -->

                                <!-- Start Ordering Field  -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Ordering</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control"  value="<?php echo $cat['Ordering']?>" name="ordering" placeholder=" Number To Arrange the Categories ">
                                </div>
                            </div>
                            <!-- End Ordering Field  -->
                            
                            <!-- Start Category Type Field  -->
                            <div class="form-group row">
                                <label class="col-lg-2 col-md-3 col-sm-12">Parent ? </label>
                                <div class="col-lg-10  col-md-9 col-sm-12">
                                    <select class="form-control selectpicker" name="parent" required>
                                        <option value="0">None</option>
                                        <?php
                                            $subCats = getAllFrom('*', 'categories','', '', 'WHERE parent = 0', '', 'ID');
                                            foreach($subCats as $subCat)
                                            {
                                                echo "<option value='".$subCat['ID']."'";
                                                    if($cat['parent'] == $subCat['ID']){ echo 'selected';}
                                                echo ">".$subCat['Name']."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- End Category Type Field  -->  

                            <!-- Start Visibility Field  -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Visible</label>
                                <div class="col-sm-2 pt-2">
                                    <input type="radio"  id="vis-yes" name="visibility" value="0" <?php if($cat['Visibility'] == 0 ) {echo "checked" ;} ?>>
                                    <label for="vis-yes">Yes</label>
                                </div>
                                <div class="col-sm-8 pt-2">
                                    <input type="radio"  id="vis-no" name="visibility" value="1" <?php if($cat['Visibility'] == 1 ) {echo "checked" ;} ?>>
                                    <label for="vis-no">No</label>
                                </div>
                            </div>
                            <!-- End Visibility Field  -->

                            <!-- Start Ads Field  -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Allow Ads</label>
                                <div class="col-sm-2 pt-2">
                                    <input type="radio"  id="ads-yes" name="ads" value="0" <?php if($cat['Allow_Ads'] == 0 ) {echo "checked" ;} ?> >
                                    <label for="ads-yes">Yes</label>
                                </div>
                                <div class="col-sm-8 pt-2">
                                    <input type="radio"  id="ads-no" name="ads" value="1" <?php if($cat['Allow_Ads'] == 1 ) {echo "checked" ;} ?> >
                                    <label for="ads-no">No</label>
                                </div>
                            </div>
                            <!-- End Ads Field  -->

                            <!-- Start Commenting Field  -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Allow Commenting</label>
                                <div class="col-sm-2 pt-2">
                                    <input type="radio"  id="com-yes" name="commenting" value="0" <?php if($cat['Allow_Comment'] == 0 ) {echo "checked" ;} ?> >
                                    <label for="com-yes">Yes</label>
                                </div>
                                <div class="col-sm-8 pt-2">
                                    <input type="radio"  id="com-no" name="commenting" value="1"  <?php if($cat['Allow_Comment'] == 1 ) {echo "checked" ;} ?> >
                                    <label for="com-no">No</label>
                                </div>
                            </div>
                            <!-- End Commenting Field  -->

                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-success font-weight-bold btn-sm"> <i class="fas fa-save"></i> Save </button>
                                    <?php
                                        if( $cat['parent'] != 0 )
                                        {
                                            echo '<a href="categories.php?action=Delete&catid='.$catid .'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </form>
                    <!-- End Edit Form  -->

    <?php 
        }else{
            $theMsg = "<div class='alert alert-danger text-center font-weight-bold'>This Category is Not Exist! </div>";
            redirectHome($theMsg,'back');
        }
        echo "</div></div>";
            // Start  Manage Items of This Category
                //Fetch All Items From Database
                $stmt = $connection ->prepare("SELECT 
                items.*, 
                users.Username
                FROM   
                        items
                    INNER JOIN
                        users
                    ON
                        users.UserID  = items.Member_ID
                    WHERE Cat_ID = ?
                    ORDER BY
                        Item_ID
                    DESC
                ");    
                $stmt ->execute(array($catid));
                //Assign Items In Variable
                $items = $stmt ->fetchAll();
                //Check If Found Items Or No
                $count = $stmt ->rowCount();
                ?>
                <!-- Start Manage Items of This Category Page Design -->
                <div class="card mb-5">
                <div class="card-body manage-items">
                    <h1 class="second-header text-center py-3">Manage [ <?php echo $cat['Name'] ?> ] Items</h1><hr>
                    <div class="table-responsive">
                    <table class="table text-center table-bordered">

                        <?php if($count > 0) {?>
                        <thead class="thead-dark">
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Adding Date</th>
                                <th>Username</th>
                                <th>Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach($items as $item){
                            echo "<tr>";
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
                                echo "<td>".$item['Username']."</td>";
                                echo "<td>
                                        <a href='items.php?action=Edit&itemid=".$item['Item_ID']."' class='btn btn-success edite-btn'><i class='fa fa-user-edit'></i> Edit</a>
                                        <a href='items.php?action=Delete&itemid=".$item['Item_ID']."' class='btn btn-danger delete-btn'><i class='fa fa-trash'></i> Delete</a>";
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
                </div>
                </div>
                <!-- End Manage Items of This Category Page Design --> 
        
    <?php echo "</div>";
    }
//End Edite Page

//Start Update Page
    elseif($action == 'Update'){ 
    
        echo " <div class='container'> <div class='card my-5'> <div class='card-body'>
                <h1 class='edit-header text-center py-3'> Update Category </h1><hr>";
               if($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    //Get Variables From Form
                    $id            = filter_var($_POST['catid'],FILTER_SANITIZE_NUMBER_INT);
                    $cat_name      = filter_var($_POST['name'] , FILTER_SANITIZE_STRING);
                    $cat_desc      = filter_var($_POST['description'] , FILTER_SANITIZE_STRING);
                    $cat_order     = filter_var($_POST['ordering'],FILTER_SANITIZE_NUMBER_INT);
                    $cat_parent    = filter_var($_POST['parent'],FILTER_SANITIZE_NUMBER_INT);
                    $cat_visible   = filter_var($_POST['visibility'],FILTER_SANITIZE_NUMBER_INT);
                    $cat_ads       = filter_var($_POST['ads'],FILTER_SANITIZE_NUMBER_INT);
                    $cat_comment   = filter_var($_POST['commenting'],FILTER_SANITIZE_NUMBER_INT);

                    if(empty($cat_name))
                    {
                        $theMsg= "<div class='alert alert-danger text-center'> Category Name Must be  <strong> Not Empty </strong> !</div>";
                        redirectHome($theMsg,'back');
                    }
                    else
                    {
                        //Update The Database With This Info
                            $stmt = $connection->prepare("UPDATE 
                                                            categories 
                                                        SET  
                                                            `Name`          = ? ,
                                                            `Description`   = ? ,
                                                            `Ordering`      = ?,
                                                            `parent`        = ?,
                                                            `Visibility`    = ?,
                                                            `Allow_Ads`     = ?, 
                                                            `Allow_Comment` = ?
                                                        WHERE 
                                                            `ID`= ? ");

                            $stmt ->execute(array( $cat_name, $cat_desc, $cat_order, $cat_parent, $cat_visible, $cat_ads, $cat_comment, $id ));

                            $stmt2 = $connection->prepare("UPDATE 
                                                                categories 
                                                            SET  
                                                                `Visibility`    = ?,
                                                                `Allow_Ads`     = ?, 
                                                                `Allow_Comment` = ?
                                                            WHERE 
                                                                `parent`= ? ");
                             $stmt2 ->execute(array($cat_visible, $cat_ads, $cat_comment, $id ));

                            //Show Success Message
                            $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Category Updated</div>";
                            redirectHome($theMsg);
                    }

                }
                //Show Error Message
                else{
                    $theMsg= "<div class='alert alert-danger text-center'>Sorry You Cant Browse This Page Directory !</div>";
                    redirectHome($theMsg);
                }
        echo "</div></div></div>";
        
    }
//End Update Page

//Start Delete Page
    elseif($action == 'Delete')
    {
        echo " <div class='container'>
                 <div class='card mt-5'>
                    <div class='card-body'><h1 class='edit-header text-center py-3'>Delete Category</h1><hr>";
        $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0 ;
        //Select All Data Depend On This ID 
        // replace CheckItem -> CountFunc 
        $check = countFunc('ID' , 'categories' , $catid);
        if($check > 0)
        {
            // If Id Exist Delete It 
            $stmt = $connection ->prepare("DELETE FROM categories WHERE `ID` = :cat_id ");
            $stmt ->bindParam(":cat_id",$catid);
            $stmt ->execute();
             
            $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Category Deleted</div>";
            redirectHome($theMsg,'back');
        }else{
            $theMsg = "<div class='alert alert-danger text-center'>This Category Is Not Exist ! </div>";
            redirectHome($theMsg);
        }
        echo "</div></div></div>";
    } 
// End Delete Page

    else
    {
        $theMsg = "<div class='alert alert-danger text-center font-weight-bold mt-3'>This Page Is Not Found !</div>";
        redirectHome($theMsg);
    }

// Start Footer
include $tpl."footer.php";

}

else
{
    //If No Session Redirect to login form
    header("Location: index.php");
    exit();
}

ob_end_flush();
?>