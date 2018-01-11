<?php
define('TABLE_MESSAGE_DETAILS', 'message_details'); 
// Connection
$json = '';
if(isset($_GET['rq'])):
	switch($_GET['rq']):
		case 'new':
			$msg = $_POST['msg'];
			$from = $_POST['mid'];
			$to = $_POST['fid'];
			if(empty($msg)){
				//$json = array('status' => 0, 'msg'=> 'Enter your message!.');
			}else{
				$qur = mysql_query("INSERT INTO ".TABLE_MESSAGE_DETAILS." (`message_id`, `phone_msg_id`, `message`, `sent_from`, `send_to`, `type`, `status`, `syncstatus`, `created_at`) VALUES ('0', '0', '$msg', '$from', '$to', 'a', '1', '1', '".time()."')");
				if($qur){
					$qurGet = mysql_query("select * from ".TABLE_MESSAGE_DETAILS." where id='".mysql_insert_id()."'");
					while($row = mysql_fetch_array($qurGet)){
						$json = array('status' => 1, 'msg' => $row['message'], 'lid' => mysql_insert_id(), 'time' => $row['created_at']);
					}
				}else{
					$json = array('status' => 0, 'message'=> 'Unable to process request.');
				}
			}
		break;
		case 'msg':
			$myid = $_POST['mid'];
			$fid = $_POST['fid'];
			$lid = $_POST['lid'];
			if(empty($myid)){

			}else{
				//print_r($_POST);
				$qur = mysql_query("select * from ".TABLE_MESSAGE_DETAILS." where `send_to`='$myid' && `sent_from`='$fid' && `status`=1");
				if(mysql_num_rows($qur) > 0){
					$json = array('status' => 1);
				}else{
					$json = array('status' => 0);
				}
			}
		break;
		case 'NewMsg':
			$myid = $_POST['mid'];
			$fid = $_POST['fid'];

			$qur = mysql_query("select * from ".TABLE_MESSAGE_DETAILS." where `send_to`='$myid' && `sent_from`='$fid' && `status`=1 order by id desc limit 1");
			while($rw = mysql_fetch_array($qur)){
				$json = array('status' => 1, 'msg' => '<div>'.$rw['message'].'</div>', 'lid' => $rw['id'], 'time'=> $rw['created_at']);
			}
			// update status
			$up = mysql_query("UPDATE ".TABLE_MESSAGE_DETAILS." SET  `status` = '0' WHERE `send_to`='$myid' && `sent_from`='$fid'");
		break;
	endswitch;
endif;

@mysql_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>