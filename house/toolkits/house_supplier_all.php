<?php
// house_supplier_all.php
// Initialize the session
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 5 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    //header("location: ../login.php");
    //exit;
}

if(isset($_SESSION['groups_in']) && strpos($_SESSION["groups_in"], "house") !== false){
    
} else {
    //header("location: ../welcome.php");
    //exit;
}
?>

<?php
// Include config file
require_once "../config.php";
$h_id = $h_param22 = 0; 
$h_name = $h_function = "";
// Define variables and initialize with empty values
// h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note
$h_param00 = $h_param01 = $h_param02 = $h_param03 = $h_param04 = $h_param05 = "";
$h_param06 = $h_param07 = $h_param08 = $h_param09 = "";
$h_param00_err = $h_param12_err = $houses_list_err = "";
$h_total = 0;
$h_index = $h_param22 = 0;
// all suppliers are read
$h_param09_total = $h_param09_page = $h_param09_page_total = 0;
$h_param09_arr = array();
//
$mysql_err = "";

// step 0, it's jumping from other pages
if(isset($_GET['query_h_id'])){
    $h_id = $_GET['query_h_id'];   
    
    if(isset($_GET['h_name'])){
        $h_name = $_GET['h_name']; 
    }
    if(isset($_GET['h_id_lease'])){
        $h_id_lease = $_GET['h_id_lease']; 
    }
} else {
    if(isset($_POST['h_param22'])){
        $h_param22 = trim($_POST["h_param22"]);   // filtering
    }
}

// step 1, query all suppliers
if($h_param09_total < 1){
    // Prepare a select statement
    $sql = "SELECT id, un_id, h_category, h_subcategory FROM tb_houses_suppliers WHERE un_id > ? ORDER BY id DESC";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters $_SESSION["id"]
        mysqli_stmt_bind_param($stmt, "d", $param_un_id);
        
        // Set parameters
        $param_un_id = 0;
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            /* store result */
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) < 1){
                $mysql_err = "Nothing in the database.";
            } else{
                mysqli_stmt_bind_result($stmt, 
                    $h_param09, $tmp_uid, $tmp_cat, $h_param12
                );
                $h_param09_total = 0;                
                while(mysqli_stmt_fetch($stmt)){
                    if($tmp_cat == 3){  // it's landlord, tenants
                        if(isset($_SESSION['id']) && $tmp_uid == $_SESSION["id"]){      
                            // only creator can see                      
                        } else {
                            continue;
                        }
                    }
                    if($h_param22 > 0 && $h_param12 != 10){
                        // if is not equal to subcategory or full repairs
                        if($h_param22 != $h_param12) continue;
                    }
                    array_push($h_param09_arr, $h_param09);
                    $h_param09_total ++;
                }        
                
            }
        } else{
            $mysql_err = "Oops! Something went wrong. Please query houses again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

}
$h_param00_arr = $h_param01_arr = $h_param02_arr = $h_param03_arr = array();
$h_param04_arr = $h_param05_arr = $h_param06_arr = $h_param07_arr = $h_param08_arr = $h_param12_arr = array();
{
    $h_param09_page_total = intval( $h_param09_total / 10 );
    if( ($h_param09_total % 10) == 0){
    } else {
        $h_param09_page_total ++;
    }    
}
// step 3, command to be called
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // save important parameters
    if(isset($_POST['h_id'])) $h_id = trim($_POST["h_id"]);    
    if(isset($_POST['h_name'])) $h_name = trim($_POST["h_name"]);    
    if(isset($_POST['h_param09_page'])) $h_param09_page = trim($_POST["h_param09_page"]);    
    if(isset($_POST['h_id_lease'])) $h_id_lease = trim($_POST["h_id_lease"]);    

    if(isset($_POST['Edit'])) { 
        $selHouses = "";
        if(isset($_POST['formHouses'])) $selHouses = $_POST['formHouses'];
  
        if(empty($selHouses)) 
        {
            $mysql_err = "You didn't select any house!";
        } 
        else 
        {
            $nMembers = count($selHouses);            
            for($ii=0; $ii < $nMembers; $ii++)
            {
                $query_sp_id = $selHouses[$ii];
                // only one will be selected
                header("location: house_supplier_manage.php?query_h_id=".$h_id."&h_name=".$h_name.
                    "&h_function=".$h_function."&query_sp_id=".$query_sp_id);
                exit;  
            }
        }
    }elseif(isset($_POST['Previous'])) {         
        if($h_param09_page > 0) $h_param09_page --;        
    }elseif(isset($_POST['Next'])) {     
        if($h_param09_page < $h_param09_page_total - 1) $h_param09_page ++;
    }elseif(isset($_POST['Filter'])) { 
        $h_param09_page = 0;
    } 
} 
// get IDs for showing in one page
{
        // get shown IDs
        $h_total = 0;
        for($ii=0; $ii < $h_param09_total; $ii++)
        {
            if($ii < ($h_param09_page * 10) ) continue;
            else if($ii >= (($h_param09_page+1) * 10) ) break;
            array_push($h_param00_arr, $h_param09_arr[$ii]);
            $h_total ++;
        }
}

// step 4, query suppliers in one page
if($h_total > 0){
    // Prepare a select statement
    $sql = "SELECT un_id, h_category, h_subcategory, h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note FROM tb_houses_suppliers WHERE id = ? ORDER BY id ASC";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters $_SESSION["id"]
        mysqli_stmt_bind_param($stmt, "d", $param_id);
        for($jj=0; $jj < $h_total; $jj++){
            // Set parameters
            $param_id = $h_param00_arr[$jj];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) < 1){
                    $mysql_err = "Nothing in the database.";
                } else{
                    mysqli_stmt_bind_result($stmt, 
                        $tmp_uid, $tmp_cat, $h_param12,
                        $h_param01, $h_param02, $h_param03,
                        $h_param04, $h_param05, $h_param06,
                        $h_param07
                    );
                    // valid read             
                    if(mysqli_stmt_fetch($stmt)){   
                        array_push($h_param01_arr, $h_param01);
                        array_push($h_param02_arr, $h_param02);
                        array_push($h_param03_arr, $h_param03);
                        array_push($h_param04_arr, $h_param04);
                        array_push($h_param05_arr, $h_param05);
                        array_push($h_param06_arr, $h_param06);
                        array_push($h_param07_arr, $h_param07);
                        array_push($h_param08_arr, $tmp_cat);
                        array_push($h_param12_arr, $h_param12);
                    } 
                }                
            } else{
                $mysql_err = "Oops! Something went wrong. Please query suppliers again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
        $i_index = $h_total - 1;       
        $h_param00 = $h_param00_arr[$i_index];
    }
}
// something is wrong
if(empty(trim($mysql_err))){
} else {
    // jump to create one
    header("location: ./house_supplier_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_id_suppliers=".$h_id_suppliers."&h_id_lease=".$h_id_lease);
    exit;
}


// Close connection
mysqli_close($link);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List all suppliers</title>
    <link rel="icon" type="image/png" href="../../img/icon_appstorego.png" sizes="32x32" />
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 640px; padding: 20px; }
    </style>
</head>
<body>
    <?php
        $account_info = "";
        if( isset($_SESSION['username']) ) $account_info = "";
        if(isset($_SESSION['username'])) $account_info = htmlspecialchars($_SESSION["username"]);
        echo '<table border="0" width="300" align="center">';
        echo "<td>".'<img src="../../img/icon_appstorego.png" alt="icon" />'."</td>";
        echo "<td>"."<a href=../>DataPublic.org</a>"."<br>"."OX Rent Cloud"."<br>"."<a href=../welcome.php>$account_info</a>"."</td>";
        echo "</table>";
    ?>
    <div class="wrapper">
        <p><?php
                echo '<a href="'."./house_supplier_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Create</a> -> Review -> <?php 
                echo '<a href="'."./house_supplier_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
        <h2>List all suppliers for you</h2>
        <p>Suppliers <?php 
            echo $h_name.' in total of '.$h_param09_total.' at page '; 
            echo $h_param09_page+1; 
            echo ' / '.$h_param09_page_total;
            ?>:</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($h_param12_err)) ? 'has-error' : ''; ?>">
                    <label for='formIdSub[]'>Filtered by subcategory</label><br>                
                    <select id="h_param22" name="h_param22">
                        <?php
                            // subcategory for filtering only
                            echo '<option value=0 '; 
                            if($h_param22 == 0) echo "selected"; 
                            echo ' >Show all</option>';
                            for ($x = 1; $x <= 11; $x++) {
                                if($x <= 10) echo '<option value='.$x.' '; 
                                else echo '<option value=99'.' '; 
                                if($h_param22 == $x) echo "selected";
                                elseif($h_param22 == 99) echo "selected";
                                if($x == 1) echo ' >'.'Plumbing.'.'</option>';
                                elseif($x == 2) echo ' >'.'Furnace.'.'</option>';
                                elseif($x == 3) echo ' >'.'A/C.'.'</option>';
                                elseif($x == 4) echo ' >'.'Electric.'.'</option>';
                                elseif($x == 5) echo ' >'.'Goverment.'.'</option>';
                                else if($x == 6) echo ' >'.'Realtor.'.'</option>';
                                else if($x == 7) echo ' >'.'Insurance.'.'</option>';
                                else if($x == 8) echo ' >'.'Finance.'.'</option>';
                                else if($x == 9) echo ' >'.'House inspection.'.'</option>';
                                else if($x == 10) echo ' >'.'Full repairs.'.'</option>';
                                else echo ' >'.'Others.'.'</option>';
                            }
                        ?>
                    </select>  
                    <input type="submit" name="Filter" value="Filter"/>             
                    <span class="help-block"><?php echo $h_param12_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param00_err)) ? 'has-error' : ''; ?>">                
                <input type="hidden" name="h_id" class="form-control" value="<?php echo $h_id; ?>" readonly="readonly">
                <input type="hidden" name="h_name" class="form-control" value="<?php echo $h_name; ?>" readonly="readonly">
                <input type="hidden" name="h_param09_page" class="form-control" value="<?php echo $h_param09_page; ?>" readonly="readonly">
                <input type="hidden" name="h_id_lease" class="form-control" value="<?php echo $h_id_lease; ?>" readonly="readonly">
                <span class="help-block"><?php echo $h_param00_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($houses_list_err)) ? 'has-error' : ''; ?>">
                <label for='formHouses[]'>Select one supplier that you want to view:</label><br>
                
                <?php
                    echo '<table border="1" width="960" align="center">';
                    //
                    for($aa = 0; $aa <= $h_total - 1; $aa++){
                        // add items
                        echo '<td><input type="radio" name="formHouses[]" value='.$h_param00_arr[$aa].'><label>'.$h_param00_arr[$aa].'</label><br/></td>';
                        if(($aa %8) == 0) echo '<td>'.'<img src="../../img/icon_service1.png" alt="icon" />'.'</td>';
                        else if(($aa %8) == 1) echo '<td>'.'<img src="../../img/icon_service2.png" alt="icon" />'.'</td>';
                        else if(($aa %8) == 2) echo '<td>'.'<img src="../../img/icon_service3.png" alt="icon" />'.'</td>';
                        else if(($aa %8) == 3) echo '<td>'.'<img src="../../img/icon_service4.png" alt="icon" />'.'</td>';
                        else if(($aa %8) == 4) echo '<td>'.'<img src="../../img/icon_service5.png" alt="icon" />'.'</td>';
                        else if(($aa %8) == 5) echo '<td>'.'<img src="../../img/icon_service6.png" alt="icon" />'.'</td>';
                        else if(($aa %8) == 6) echo '<td>'.'<img src="../../img/icon_service7.png" alt="icon" />'.'</td>';
                        else if(($aa %8) == 7) echo '<td>'.'<img src="../../img/icon_service.png" alt="icon" />'.'</td>';
                        else echo '<td>'.'<img src="../../img/icon_service.png" alt="icon" />'.'</td>';
                        //
                        echo '<td>'.'<a href="./house_supplier_manage.php?query_h_id='.$h_id."&h_name=".$h_name.
                            "&h_function=".$h_function."&query_sp_id=".$h_param00_arr[$aa].'">'.
                            $h_param01_arr[$aa].'</a>'.'</td>';
                        $x = $h_param08_arr[$aa];
                        if($x == 1) echo '<td>'.'Service.'.'</td>';
                        else if($x == 2) echo '<td>'.'Supplier.'.'</td>';
                        else if($x == 3) echo '<td>'.'Landlord,tenants.'.'</td>';
                        else echo '<td>'.'Others.'.'</td>';
                        // subcategory
                        $x = $h_param12_arr[$aa];
                        if($x == 1) echo '<td>'.'Plumbing.'.'</td>';
                        else if($x == 2) echo '<td>'.'Furnace.'.'</td>';
                        else if($x == 3) echo '<td>'.'A/C.'.'</td>';
                        else if($x == 4) echo '<td>'.'Electric.'.'</td>';
                        else if($x == 5) echo '<td>'.'Goverment.'.'</td>';
                        else if($x == 6) echo '<td>'.'Realtor.'.'</td>';
                        else if($x == 7) echo '<td>'.'Insurance.'.'</td>';
                        else if($x == 8) echo '<td>'.'Finance.'.'</td>';
                        else if($x == 9) echo '<td>'.'House inspection.'.'</td>';
                        else if($x == 10) echo '<td>'.'Full repairs.'.'</td>';
                        else echo '<td>'.'Others.'.'</td>';
                        echo '<td>'.$h_param02_arr[$aa].' '.'</td>';
                        echo '<td>'.$h_param03_arr[$aa].' '.'</td>';
                        echo '<td>'.$h_param04_arr[$aa].' '.'</td>';
                        echo '<td>'.$h_param05_arr[$aa].' '.'</td>';
                        if(strpos($h_param06_arr[$aa], "http") !== false){
                            echo '<td>'.'<a href="'.$h_param06_arr[$aa].'">'.$h_param06_arr[$aa].'</a>'.' '.'</td>';
                        } else {
                            echo '<td>'.'<a href="http://'.$h_param06_arr[$aa].'">'.$h_param06_arr[$aa].'</a>'.' '.'</td>';
                        }
                        echo '<td>'.$h_param07_arr[$aa].' '.'</td>';
                        echo "</tr>";                      
                        // 
                    }
                    if($h_total < 1){
                        echo '<td>'.'<a href="'.
                        "./house_supplier_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'.
                        'Create</a> your first supplier now.'.'</td>';
                    }
                    echo "</table>";
                ?>
               
                <span class="help-block"><?php echo $houses_list_err; ?></span>
            </div>


            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Previous" value="Previous Page">
                <input type="submit" class="btn btn-primary" name="Edit" value="Edit">
                <input type="submit" class="btn btn-default" name="Next" value="Next Page">
            </div>
            <p><?php echo '<span style="color:#F00;text-align:center;">'.$mysql_err.'</span>'; ?></p>
            <p><a href="../">Home page</a>.</p>
            <p></p>
            <p><?php
                echo '<a href="'."./house_supplier_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Create</a> -> Review -> <?php 
                echo '<a href="'."./house_supplier_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
            <p><img src="../../img/ox_rent_qr100.png" alt="icon" />Copyright @2020 <a href="../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>