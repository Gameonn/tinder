<?php
session_start();
require_once('../php_include/db_connection.php');
require_once("../php_include/header.php");
require_once("../php_include/sidebar.php");

global $conn;
$sql="select temp.* from (SELECT id,username,gender,profile_pic,age,city,(select count(*) from user_like uu where uu.liked_to=users.id and uu.status=1 and (select count(*) FROM user_like ul where ul.liked_by=users.id and ul.liked_to=uu.liked_by and ul.status=1)) as match_count FROM `users` WHERE 1 ) temp where temp.match_count > 0 ORDER BY match_count DESC";
$stmt=$conn->prepare($sql);
try{
  $stmt->execute();
  $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch(Exception $e){}

//print_r($result);die;

function getMatchedUsers($user_id){

global $conn;
$sql="SELECT users.username,users.profile_pic
FROM `user_like`  
	join users on users.id=user_like.liked_to
	WHERE liked_by=:user_id and user_like.status=1 and liked_to IN (select liked_by from user_like where liked_to=:user_id and user_like.status=1)";
    //echo $sql;die;
$stmt=$conn->prepare($sql);
$stmt->bindValue('user_id',$user_id);
try{$stmt->execute();}
catch(Exception $e){
    echo $e->getMessage();
}
$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
return $result;
}
?>

<section id="main-content">
<section class="wrapper">
<center></center>
<div class="row">

<div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="">
                <div class="col-sm-8" style="color: #1FB5AD; font-family: sans-serif; font-size: xx-large;">Users </div>        
			<div class="col-md-4" style="margin-top:10px;">				
			<form action="getCSV.php" method ="post" > 
			<input type="hidden" name="csv_text" id="csv_text">
			<button type="submit" onclick="getCSVData()" class="btn btn-primary" ><i class="fa fa-bars"></i> Export Users</button>
			</form>
			</div>	
                    </header>
                    <div class="panel-body">
                    <div class="adv-table">
                    <table  class="display table table-bordered table-striped" id="dynamic-table">
                    <thead>
                    <tr>
                        <th>Profile Image</th>
                        <th>Username</th>
                        <th class="hidden-phone">Matched Users</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach($result as $value){ ?>

                    <tr class="gradeX">
                        <td><img src="../uploads/<?php echo $value['profile_pic']; ?>" style="width: 100px;"></td>
                        <td><?php echo $value['username']; ?></td>
                        <td class="center hidden-phone"><?php $matches = getMatchedUsers($value['id']);
                            if($matches){
                                foreach ($matches as $rr => $ss) {
                                    echo '<a><div style="float:left;padding:4px;">';
                                    echo '<img src="../uploads/'.$ss['profile_pic'].'" style="width: 100px" alt="'.$ss['username'].'"/>';
                                    echo "</div><a/>";
                                }
                            }
                         ?></td>
                    </tr>
					<?php } ?>
                  <!--  <tfoot>
                    <tr>
                        <th>Profile Image</th>
                        <th>Username</th>
                        <th>Gender</th>
                        <th class="hidden-phone">Age</th>
                        <th class="hidden-phone">City</th>
                    </tr>
                    </tfoot> -->
                    </table>
                    </div>
                    </div>
                </section>
            </div>
        </div>
		
</div>
</section>
</section>
<!--main content end-->

</section>

<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/bootstrap-switch.js"></script>
<script src="js/jquery.customSelect.min.js" ></script>

<!--dynamic table-->
<script type="text/javascript" language="javascript" src="js/advanced-datatable/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/data-tables/DT_bootstrap.js"></script>
<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script type="text/javascript" src="js/table2CSV.js"></script>

<!--toggle initialization-->
<script src="js/toggle-init.js"></script>

<!--clock init-->
<script src="js/css3clock/js/css3clock.js"></script>

<script type="text/javascript">
    //custom select box

    $(function(){
        $('select.styled').customSelect();
    });
</script>
<script>
function getCSVData(){
var csv_value=$('#dynamic-table').table2CSV({delivery:'value'});
$("#csv_text").val(csv_value);
}
</script>
<script type="text/javascript">
            $(function() {
                $('#dynamic-table').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
            });
        </script>

</body>
</html>
  		