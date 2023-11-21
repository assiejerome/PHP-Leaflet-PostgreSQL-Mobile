<?php
    include "../../includes/init.php";
    if (isset($_POST['tbl'])) {
        $table = $_POST['tbl'];
        if (isset($_POST['user'])){
            $user = $_POST['user'];
            try {
                $result = $pdo->query("SELECT id, name, modified FROM {$table} WHERE createdby='{$user}' ORDER BY modified DESC, id DESC");
                $returnTable="<table class='table table-hover'>";
                $returnTable.="<tr class='tblHeader'><th>ID</th><th>Name</th><th>Modified</th><th></th><th></th><th></th></tr>";
                foreach($result AS $row) {
                    $returnTable.="<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>".substr($row['modified'], 0, 16)."</td><td><span class='btnFindGen'  data-id='{$row['id']}' data-table='{$table}'><i class='fa fa-search'></i></span></td><td><span class='btnNavGen'  data-id='{$row['id']}' data-table='{$table}'><i class='fa fa-ship'></i></span></td><td><span class='btnEditGen' data-id='{$row['id']}' data-table='{$table}'><i class='fa fa-pencil'></i></span></td><td><span class='btnDeleteGen' data-id='{$row['id']}' data-table='{$table}'><i class='fa fa-trash'></i></span></td></tr>";
                }
                $returnTable.="</table>";
                echo $returnTable;
            } catch(PDOException $e) {
                echo "ERROR: ".$e->getMessage();
            }
        } else {
            echo "ERROR: No user parameter incuded with request";
        }
    } else {
        echo "ERROR: No table parameter incuded with request";
    }
?>