    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">Design Jérôme</span>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/<?php  echo $root_directory?>/index.php">Home</a></li>
                    <li><a href="/<?php  echo $root_directory?>/page1.php">Page 1</a></li>
                    <li><a href="/<?php  echo $root_directory?>/page2.php">Page 2</a></li>
                    <li><a href="/<?php  echo $root_directory?>/content/generic_mobile.php">Carte</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php
                        if (logged_in()) {
                            echo "<li><a href='/{$root_directory}/mycontent.php'>{$_SESSION['username']}'s Content</a></li>";
                            echo "<li><a href='/{$root_directory}/logout.php'><span class='glyphicon glyphicon-log-out'></span> Logout</a></li>";
                        } else {
                            echo "<li><a href='/{$root_directory}/login.php'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>";
                            echo "<li><a href='/{$root_directory}/register.php'><span class='glyphicon glyphicon-user'></span> Register</a></li>";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    
