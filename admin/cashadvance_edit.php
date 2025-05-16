<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$amount = $_POST['amount'];

		// Get current cash advance record with employee and rate info
		$sql = "SELECT ca.employee_id, p.rate, ca.amount AS current_amount 
				FROM cashadvance ca 
				JOIN employees e ON ca.employee_id = e.id 
				JOIN position p ON e.position_id = p.id 
				WHERE ca.id = '$id'";
		$query = $conn->query($sql);
		
		if($query->num_rows < 1){
			$_SESSION['error'] = 'Cash advance record not found';
		} else {
			$row = $query->fetch_assoc();
			$employee_id = $row['employee_id'];
			$rate = $row['rate'];
			$current_amount = $row['current_amount'];

			// Max advance for 2 weeks (8 hours/day * 10 days)
			$max_advance = $rate * 8 * 10;

			// Sum of cash advances in last 14 days excluding this record
			$sql_adv = "SELECT SUM(amount) AS total_advance FROM cashadvance 
						WHERE employee_id = '$employee_id' 
						AND date_advance >= DATE_SUB(NOW(), INTERVAL 14 DAY)
						AND id != '$id'";
			$result = $conn->query($sql_adv);
			$data = $result->fetch_assoc();
			$total_advance = $data['total_advance'] ?? 0;

			// New total = total other advances + new amount from edit form
			$new_total = $total_advance + $amount;

			if($new_total > $max_advance){
				$_SESSION['error'] = "Amount exceeds maximum allowed advance for 2 weeks (₱" . number_format($max_advance, 2, '.', ',') . ")";
			} else {
				$sql_update = "UPDATE cashadvance SET amount = '$amount' WHERE id = '$id'";
				if($conn->query($sql_update)){
					$_SESSION['success'] = 'Cash advance updated successfully';
				} else {
					$_SESSION['error'] = $conn->error;
				}
			}
		}
	} else {
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: cashadvance.php');
?>
