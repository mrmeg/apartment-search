<?php 
require_once("conn/connApts.php"); 
$IDapt = $_GET['IDapt'];
$query = "SELECT * FROM apartments, buildings, images WHERE apartments.bldgID = buildings.IDbldg AND IDapt = '$IDapt';";
$imageQuery = "SELECT * FROM apartments, images WHERE apartments.IDapt = images.foreignID AND IDapt = '$IDapt' AND catID = 1;";
$result = mysqli_query($conn, $query);
$imageResult = mysqli_query($conn, $imageQuery);
$row = mysqli_fetch_array($result);
$imageRow = mysqli_fetch_array($imageResult);
$row_cnt = $imageResult->num_rows;
echo mysqli_error($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apartment <?php echo $row['apt']; ?> Details</title>
    <link href="css/apts.css" rel="stylesheet">
</head>
<body>
<style>
    table, tr, td {
        border: 1px solid black;
        min-width:10px;
        min-height:10px;
    }
</style>
    <table>
        <tr>
            <td colspan="3"><h1>Apartment <?php echo $row['apt']; ?> Details</h1></td>
            
        </tr>
        <tr>
            <td rowspan="2">
                <img width="200px" id="mainPic" src="images/propPics/<?php echo $imageRow['imgName'] == '' ? "SohoLoftsApt2.jpg": $imageRow['imgName']; ?>">
                <?php if($row_cnt > 1){ 
                    echo "<br>";
                    for($i = 1; $i < $row_cnt; $i++){
                        $imageRow = mysqli_fetch_array($imageResult);
                        echo '<img onclick="switchImage()" width="30px" src="images/propPics/' . $imageRow['imgName'] . '" id="' . $imageRow['imgName'] . '">';
                    }
                }; ?>
            </td>
            <td>Square Feet: <?php echo $row['sqft']; ?></td>
            <td>Rent: $<?php echo $row['rent']; ?></td>
        </tr>
        <tr>
            <td colspan="2"><?php echo $row['aptDesc']; ?></td>
        </tr>
        <tr>
            <td>Address: <?php echo $row['address']; ?></td>
            <td>Phone: <?php echo $row['phone']; ?></td>
            <td>Email: <?php echo $row['email']; ?></td>
        </tr>
    </table>
    <br>
    <button class="backButt" onclick="goBack()" width="100px">Click to go back</button>

    <script>
		function goBack() {
			window.history.back();
		}
        function switchImage() {
            let mainPic = document.getElementById('mainPic');
            let mainPicSrc = mainPic.src;
            let smallSrc = event.target.src;
            mainPic.src = smallSrc;
            event.target.src = mainPicSrc;
        }
    </script>
</body>
</html>