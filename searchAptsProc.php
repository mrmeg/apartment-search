<html>
    <link href="css/apts.css" rel="stylesheet">
</html>

<?php 

// 1.) there is no form to process, so skip the POST / GET vars part
$bdrms = $_GET['bdrms'];
$baths = $_GET['baths'];
$minRent = $_GET['minRent'];
$maxRent = $_GET['maxRent'];
$bldgID = $_GET['bldgID'];
$orderBy = $_GET['orderBy'];
$ascDesc = $_GET['ascDesc'];



// 2 + 3.) Connect to mysql, and select the database
require_once("conn/connApts.php");

// 4.) write out the CRUD "order" (query) -- what you want to do
$query = "SELECT * from apartments, buildings, neighborhoods
WHERE apartments.bldgID = buildings.IDbldg 
AND buildings.hoodID = neighborhoods.IDhood 
AND rent BETWEEN '$minRent' AND '$maxRent'";

// concat the query if the user chooses a building from the dynamic menu
if($bldgID != -1) { // if the user selects something other than ANY
    $query .= " AND bldgID='$bldgID'";
}

//Concat the query for bdrms and baths if menu choice is not "any"
if($bdrms != -1) { //if anything but "any" is selected
    //if it is a plus-sign choice or not (1+, 2+)
    //if rounding off bdrms does NOT change the value, then
    //bdrms is already an integer
    if($bdrms == round($bdrms)) {
        $query .= " AND bdrms='$bdrms'";
    }else { //rounding DID change value, bdrms is NOT an integer
        $bdrms = round($bdrms);
        $query .= " AND bdrms >='$bdrms'";
    }// end if-else 
}//end if

//Do the smae for baths
if($baths != -1) {
    //multiply baths by 10 to remove the decimal places
    $baths10 = $baths * 10; //1.5 becomes 15
    //is there a remainder after the modulus operation?
    if($baths10 % 5 == 0) { // if value is an integer
        $query .= " AND baths='$baths'";
    }else { //there is a remainder
        // 
        $baths -= 0.1;
        $query .= " AND baths >= '$baths'";
    }//end if-else
}//end if


//      ---------- KEYWORD SEARCH ----------
if($_GET['search'] != "") { //true if the user typed something into the search
    $search = $_GET['search'];
    $query .= " AND (aptDesc LIKE '%$search%'              
                OR bldgDesc LIKE '%$search%'
                OR hoodDesc LIKE '%$search%'
                OR bldgName LIKE '%$search%'
                OR aptTitle LIKE '%$search%'
                OR address LIKE '%$search%')";
}

// concat query for checkboxes -- "check" to see, one by one, if the checkboxes are actually checked
if(isset($_GET['doorman'])) { // is the doorman variable set. if so it came over from the form, meaning doorman was checked
    $query .= " AND isDoorman=1";
}

if(isset($_GET['pets'])) { 
    $query .= " AND isPets=1";
}

if(isset($_GET['parking'])) { 
    $query .= " AND isParking=1";
}

if(isset($_GET['gym'])) { 
    $query .= " AND isGym=1";
}

$query .= " ORDER BY $orderBy $ascDesc";

//$query .= " ORDER BY sqft DESC"; //THIS IS THE LAST LINE OF THE QUERY ------

  // Order by *columnName* *ASC/DESC* <-- Sort based on a column

// 5.) execute the order: read records from apartments table

$result = mysqli_query($conn, $query);  // the result will be an array of arrays (or, a multi-dimensional array)

?>

<!doctype html>

<html lang="en-us">
    
<head>
    
    <meta charset="utf-8">
    
    <title>Member Join Processor</title>
    
</head>

<body>
    
    
    
    <table width="800" border="1" cellpadding="5">
    
    <tr>
        <td colspan="14" align="center">
            <h1 align="center">Lofty Heights Apartments</h1>
            <h2><?php echo mysqli_num_rows($result); ?> Results Found</h2>
            </td>
        </tr>
        
        <?php 
            if(mysqli_num_rows($result) == 0) { //no results found
                echo '
                <tr>
                  <td colspan="14">
                        <h3 align="center">Sorry! No results found! Please try again!
                    <br>
                        <button onclick="window.history.back()">Go Back</button>
                        Redirecting...
                    </h3>
                  </td>
                </tr>';
            header("Refresh:3; url=searchApts.php", true, 303);
            }else { // at least one result
                echo '<tr>
                    <th>ID</th>
                    <th>Apt</th>
                    <th>Building</th>
                    <th>Bedrooms</th>
                    <th>Baths</th>
                    <th>Rent</th>
                    <th>Floor</th>
                    <th>Sqft</th>
                    <th>Status</th>
                    <th>Neighborhood</th>
                    <th>Doorman</th>
                    <th>Pets</th>
                    <th>Gym</th>
                    <th>Parking</th>
                </tr>';
            }
        ?>

        
        <?php
        while($row = mysqli_fetch_array($result)) { ?>
          
          <tr>
              <td><?php echo $row['IDapt']; ?></td>
              <td><?php echo $row['apt']; ?></td>
              
              <td>
              
            <?php 
              echo '<a href="bldgDetails.php?bldgID=' 
                  . $row['bldgID'] . '">' 
                  . $row['bldgName'] . '</a>';
            ?>
              
              </td>
              
              <td><?php
                              
                  // ternary as alternative to if-else
                  echo $row['bdrms'] == 0 ? 'Studio' : $row['bdrms'];
                           
                  // if-else version of the ternary above
//                  if($row['bdrms'] == 0) {
//                     echo 'Studio'; 
//                  } else {
//                      echo $row['bdrms'];
//                  }
                                                  
              ?>
              
              </td>
              <td><?php echo $row['baths']; ?></td>
              <td><?php echo number_format($row['rent']); ?></td>
              <td><?php echo $row['floor']; ?></td>
              <td><?php echo $row['sqft']; ?></td>
              <td>
                <?php 
                    if($row['isAvail'] == 0) {
                      echo "Occupied";
                    } else { // value is 1
                      echo "Available";
                    }                
                ?>
              
              </td>
              <td><?php echo $row['hoodName']; ?></td>
              <td>
                  
                <?php 
              
                    if($row['isDoorman'] == 0) {
                      echo 'No'; 
                    } else {
                      echo 'Yes';
                    }
              
                ?>
              
              </td>
              
              <td><?php echo $row['isPets'] == 0 ? 'No':'Yes'; ?></td>
              
              <td><?php echo $row['isGym'] == 0 ? 'No':'Yes'; ?></td>
              
              <td><?php echo $row['isParking'] == 0 ? 'No':'Yes'; ?></td>
              
          </tr>
        
        <?php } ?>
    
    </table>
    
</body>
   
</html>