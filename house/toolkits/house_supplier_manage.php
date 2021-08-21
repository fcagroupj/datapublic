<?php
// house_supplier_manage.php
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
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        header("location: ../login.php");
        exit;
    }
}

if(isset($_SESSION['groups_in']) && strpos($_SESSION["groups_in"], "house") !== false){
    
} else {
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        header("location: ../welcome.php");
        exit;
    }
}
?>

<?php
// Include config file
require_once "../config.php";
$h_function = $h_name = ""; 
$h_id = 0;
// Define variables and initialize with empty values
// h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note
$h_param00 = $h_param01 = $h_param02 = $h_param03 = $h_param04 = $h_param05 = "";
$h_param06 = $h_param07 = $h_param11 = $h_param12 = "";
$h_param00_err = $h_param01_err = $h_param02_err = $h_param03_err = $h_param04_err = $h_param05_err = "";
$h_param06_err = $h_param07_err = $h_param11_err = $h_param12_err = "";
$h_total = 0;
$h_index = $un_id = $query_sp_id = 0;
$h_param00_arr = array();
$mysql_err = "";
// step 0, it's jumping from other pages
if(isset($_GET['query_h_id'])){
    $h_id = $_GET['query_h_id'];   
    
    if(isset($_GET['h_name'])){
        $h_name = $_GET['h_name']; 
    }
    if(isset($_GET['h_function'])){
        $h_function = $_GET['h_function']; 
    }
    if(isset($_GET['query_sp_id'])){
        $query_sp_id = $_GET['query_sp_id']; 
    }
} else {
    if(isset($_POST['query_sp_id'])) $query_sp_id = trim($_POST["query_sp_id"]); 
    if(isset($_POST['h_function'])) $h_function = trim($_POST["h_function"]); 
}
// step 1, query all suppliers
if($h_total < 1){
    // Prepare a select statement
    $sql = "SELECT id, un_id, h_category FROM tb_houses_suppliers WHERE un_id > ? ORDER BY id ASC";
    
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
                    $h_param00, $tmp_uid, $tmp_cat
                );
                $h_total = 0;                
                while(mysqli_stmt_fetch($stmt)){
                    if($tmp_cat == 3){  // it's landlord, tenants
                        if(isset($_SESSION['id']) && $tmp_uid == $_SESSION["id"]){      
                            // only creator can see                      
                        } else {
                            continue;
                        }
                    }
                    array_push($h_param00_arr, $h_param00);
                    $h_total ++;
                }        
                //$h_total = mysqli_stmt_num_rows($stmt); 
                $h_index = $h_total - 1;     
                $h_param00 = $h_param00_arr[$h_index];      
                
            }
        } else{
            $mysql_err = "Oops! Something went wrong. Please query houses again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

} 
// if it's jumping from other pages
if($query_sp_id > 0){    
    $h_param00 = $query_sp_id;
    // get h_index
    for ($x = 0; $x < $h_total; $x++) {
        if($query_sp_id == $h_param00_arr[$x]){
            $h_index = $x;
            //$mysql_err = "Match index ".$x.' sp_id '.$h_param00_arr[$x];
            break;
        }
    }      
    //$mysql_err = ' query_sp_id '.$query_sp_id;
}
// step 2, identify which ID is used
$is_update = 0;
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //echo "REQUEST_METHOD is POST";
    $i_index = 0;
    for ($x = 0; $x < $h_total; $x++) {
        if($_POST["h_param00"] == $h_param00_arr[$x]){
            $i_index = $x;
            break;
        }
    }
    if(isset($_POST['Previous'])) { 
        //act_show_previous();        
        $i_index --;
        if($i_index < 0) $i_index = 0;
        
        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
        
    } elseif(isset($_POST['Next'])) { 
        //act_show_next();
        $i_index ++;
        if($i_index < 0) $i_index = 0;
        if($i_index > $h_total-1) $i_index = $h_total-1;

        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
    } elseif(isset($_POST['Apply'])) { 
        // looking for member in the array        
        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
        // Check input errors before inserting in database
        $is_update = 1;
    } elseif(isset($_POST['Delete'])) { 
        // looking for member in the array        
        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
        // Check input errors before inserting in database
        $is_update = 4;
        if (strpos($h_function, 'delete') !== false){
            $is_update = 8;
        }
    } else {
        // looking for member in the array        
        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
    }
}
// step 3, Processing form data when form is submitted
if($is_update == 1){
    // Validate h_param01
    if(empty(trim($_POST["h_param01"]))){
        $h_param01_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param01"])) < 2){
        $h_param01_err = "The param is too short.";
    } else{
        $h_param01 = trim($_POST["h_param01"]);
    }
    // Validate h_param02
    if(empty(trim($_POST["h_param02"]))){
        $h_param02_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param01"])) < 2){
        $h_param02_err = "The param is too short.";
    } else{
        $h_param02 = trim($_POST["h_param02"]);
    }
    // Validate h_param03
    if(empty(trim($_POST["h_param03"]))){
        $h_param03_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param03"])) < 2){
        $h_param03_err = "The param is too short.";
    } else{
        $h_param03 = trim($_POST["h_param03"]);
    }
    // Validate h_param04
    if(empty(trim($_POST["h_param04"]))){
        $h_param04_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param04"])) < 2){
        $h_param04_err = "The param is too short.";
    } else{
        $h_param04 = trim($_POST["h_param04"]);
    }
    // Validate h_param05
    if(empty(trim($_POST["h_param05"]))){
        $h_param05_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param05"])) < 2){
        $h_param05_err = "The param is too short.";
    } else{
        $h_param05 = trim($_POST["h_param05"]);
    }
    // Validate h_param06
    if(empty(trim($_POST["h_param06"]))){
        $h_param06_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param06"])) < 2){
        $h_param06_err = "The param is too short.";
    } else{
        $h_param06 = trim($_POST["h_param06"]);
    }
    // Validate h_param07
    if(empty(trim($_POST["h_param07"]))){
        $h_param07_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param07"])) < 2){
        $h_param07_err = "The param is too short.";
    } else{
        $h_param07 = trim($_POST["h_param07"]);
    }
    // Validate h_param11
    if(empty(trim($_POST["h_param11"]))){
        $h_param11_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param11"])) < 1){
        $h_param11_err = "The param is too short.";
    } else{
        $h_param11 = trim($_POST["h_param11"]);
    }
    // Validate h_param12
    if(empty(trim($_POST["h_param12"]))){
        $h_param12_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param12"])) < 1){
        $h_param12_err = "The param is too short.";
    } else{
        $h_param12 = trim($_POST["h_param12"]);
    }

    $un_id = trim($_POST["un_id"]);
    if(! isset($_SESSION['id']) ){
        $mysql_err = "You have no permission to change, please login in.";
    }elseif($un_id != $_SESSION["id"]){
        $mysql_err = "You have no permission to change, please create one.";
    // Check input errors before inserting in database
    }elseif( empty($h_param01_err) && empty($h_param02_err) && empty($h_param03_err) && 
        empty($h_param04_err) && empty($h_param05_err) && empty($h_param06_err) && 
        empty($h_param07_err) && empty($h_param11_err) && empty($h_param12_err) ){
        // Prepare a update statement
        $sql = "UPDATE tb_houses_suppliers SET h_category = ?, h_subcategory = ?, h_company = ?, h_contact = ?, h_telephone = ?, h_address = ?, h_email = ?, h_website = ?, h_note = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ddsssssssd", 
                $param_h_param11, $param_h_param12, 
                $param_h_param01, $param_h_param02, $param_h_param03, 
                $param_h_param04, $param_h_param05, $param_h_param06, 
                $param_h_param07, $param_h_param00 
                );
            
            // Set parameters
            $param_h_param00 = $h_param00;
            $param_h_param01 = $h_param01;
            $param_h_param02 = $h_param02;
            $param_h_param03 = $h_param03;
            $param_h_param04 = $h_param04;
            $param_h_param05 = $h_param05;
            $param_h_param06 = $h_param06;
            $param_h_param07 = $h_param07;
            $param_h_param11 = $h_param11;
            $param_h_param12 = $h_param12;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
            } else{
                $mysql_err = "Oops! Something went wrong. Please update again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }        
    }
} else if($is_update == 4){  // will delete from database
    $un_id = trim($_POST["un_id"]);
    if(! isset($_SESSION['id']) ){
        $mysql_err = "You have no permission to change, please login in.";
    }elseif($un_id != $_SESSION["id"]){
        $mysql_err = "You have no permission to change, please create one.";
    // Check input errors before deleting in database
    }else{
        $h_function = 'delete';
        header("location: ./house_supplier_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.
            '&query_sp_id='.$query_sp_id);
        exit;
    }
} else if($is_update == 8){  // delete from database
    $un_id = trim($_POST["un_id"]);
    if(! isset($_SESSION['id']) ){
        $mysql_err = "You have no permission to change, please login in.";
    }elseif($un_id != $_SESSION["id"]){
        $mysql_err = "You have no permission to change, please create one.";
    // Check input errors before deleting in database
    }else{
        // delete and go to list
        $sql = "DELETE FROM tb_houses_suppliers WHERE id=?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "d", $param_h_param00);
            
            // Set parameters
            $param_h_param00 = $h_param00;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /*  result */
                header("location: ./house_supplier_all.php");
                exit;
            } else{
                $mysqli_err = "Oops! Something went wrong. Please delete it again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }        
    }    
} else {  // or read database
    if(empty(trim($mysql_err))){
        // read house from tb_houses_global
        // Prepare a select statement
        $sql = "SELECT un_id, h_category, h_subcategory, h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note FROM tb_houses_suppliers WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "d", $param_h_param00);
            
            // Set parameters
            $param_h_param00 = $h_param00;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $un_id,
                        $h_param11, $h_param12, 
                        $h_param01, $h_param02, $h_param03,
                        $h_param04, $h_param05, $h_param06,
                        $h_param07
                    );
                                    
                    if(mysqli_stmt_fetch($stmt)){                        
                    }    
                    //  date , not datetime
                }
            } else{
                $mysqli_err = "Oops! Something went wrong. Please query houses again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }        
    }
}
// Close connection
mysqli_close($link);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage a house supplier</title>
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
        <h2>Manage a house supplier</h2>
        <p>Supplier No. <?php echo $h_index+1; ?> / <?php echo $h_total; ?> :</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($h_param00_err)) ? 'has-error' : ''; ?>">
                <input type="hidden" name="query_sp_id" class="form-control" value="<?php echo $query_sp_id; ?>" readonly="readonly">
                <input type="hidden" name="h_function" class="form-control" value="<?php echo $h_function; ?>" readonly="readonly">
                <input type="hidden" name="h_param00" class="form-control" value="<?php echo $h_param00; ?>" readonly="readonly">
                <input type="hidden" name="un_id" class="form-control" value="<?php echo $un_id; ?>" readonly="readonly">
                <span class="help-block"><?php echo $h_param00_err; ?></span>
            </div>    
            
            <div class="form-group <?php echo (!empty($h_param11_err)) ? 'has-error' : ''; ?>">
                <label for='formId[]'>Category</label><br>                
                <select id="h_param11" name="h_param11">
                    <?php
                        // 
                        echo '<option value=0 '; 
                        if($h_param11 == 0) echo "selected"; 
                        echo ' >please select</option>';
                        for ($x = 1; $x <= 4; $x++) {
                            echo '<option value='.$x.' '; 
                            if($h_param11 == $x) echo "selected";
                            if($x == 1) echo ' >'.'Service.'.'</option>';
                            elseif($x == 2) echo ' >'.'Supplier.'.'</option>';
                            elseif($x == 3) echo ' >'.'Landlord,tenants.'.'</option>';
                            elseif($x == 4) echo ' >'.'Others.'.'</option>';
                        }
                    ?>
                </select>               
                <span class="help-block"><?php echo $h_param11_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param12_err)) ? 'has-error' : ''; ?>">
                <label for='formIdSub[]'>Subcategory</label><br>                
                <select id="h_param12" name="h_param12">
                    <?php
                        // subcategory selected
                        echo '<option value=0 '; 
                        if($h_param12 == 0) echo "selected"; 
                        echo ' >please select</option>';
                        for ($x = 1; $x <= 11; $x++) {
                            if($x <= 10) echo '<option value='.$x.' '; 
                            else echo '<option value=99'.' '; 
                            if($h_param12 == $x) echo "selected";
                            elseif($h_param12 == 99) echo "selected";
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
                <span class="help-block"><?php echo $h_param12_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param01_err)) ? 'has-error' : ''; ?>">
                <label>Name of company</label>
                <input type="text" name="h_param01" class="form-control" value="<?php echo $h_param01; ?>">
                <span class="help-block"><?php echo $h_param01_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($h_param02_err)) ? 'has-error' : ''; ?>">
                <label>Contact person</label>
                <input type="text" name="h_param02" class="form-control" value="<?php echo $h_param02; ?>">
                <span class="help-block"><?php echo $h_param02_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param03_err)) ? 'has-error' : ''; ?>">
                <label>Telephone</label>                
                <input type="tel" name="h_param03" placeholder="888-000-0000" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" maxlength="12"  value="<?php echo $h_param03; ?>" required/> 
                <span class="help-block"><?php echo $h_param03_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param04_err)) ? 'has-error' : ''; ?>">
                <label>Address</label>
                <input type="text" name="h_param04" class="form-control" value="<?php echo $h_param04; ?>">
                <span class="help-block"><?php echo $h_param04_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param05_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="h_param05" class="form-control" value="<?php echo $h_param05; ?>">
                <span class="help-block"><?php echo $h_param05_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param06_err)) ? 'has-error' : ''; ?>">
                <label>Website</label>
                <input type="text" name="h_param06" class="form-control" value="<?php echo $h_param06; ?>">
                <span class="help-block"><?php echo $h_param06_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param07_err)) ? 'has-error' : ''; ?>">
                <label>Note</label>
                <input type="text" name="h_param07" class="form-control" value="<?php echo $h_param07; ?>">
                <span class="help-block"><?php echo $h_param07_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Previous" value="Previous">
                <input type="submit" class="btn btn-primary" name="Apply" value="Save now">
                <input type="submit" class="btn btn-default" name="Next" value="Next">
            </div>
            <div class="form-group">
                <?php
                    echo '<input type="submit" class="btn btn-default" name="Delete" value="';
                    if (strpos($h_function, 'delete') !== false) echo 'Confirm delete';
                    else echo 'Delete';
                    echo '">';
                ?>
            </div>
            <p><?php echo '<span style="color:#F00;text-align:center;">'.$mysql_err.'</span>'; ?></p>
            <p><a href="./house_supplier_all.php">Go to list all suppliers</a>.</p>
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