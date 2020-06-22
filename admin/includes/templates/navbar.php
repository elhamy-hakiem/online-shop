<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        
        <a class="navbar-brand" href="dashboard.php"><?php echo lang('HOME_ADMIN'); ?></a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-nav" aria-controls="app-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="app-nav">
            <ul class="navbar-nav mr-auto">

                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php"><?php echo lang('HOME') ?> <span class="sr-only">(current)</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="categories.php"><?php echo lang('CATEGORIES') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="items.php"><?php echo lang('ITEMS') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="members.php"><?php echo lang('MEMBERS') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="comments.php"><?php echo lang('COMMENTS') ?></a>
                </li>
                
            </ul>

            <ul class="navbar-nav ml-auto mr-5 pr-2">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       <i class="fas fa-user"></i>  <?php echo $_SESSION['Username'];?>
                    </a>
                    <div class="dropdown-menu " aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="../index.php" target="_blank">Visit Shop</a>
                        <a class="dropdown-item" href="members.php?action=Edit&userid=<?php echo $_SESSION['ID'];?>">
                            <?php echo lang('Edit_Profile') ?>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php"><?php echo lang('Logout')?></a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>