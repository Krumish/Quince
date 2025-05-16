<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$employee = $_POST['employee'];
		$amount = $_POST['amount'];
		
		// Get employee info with rate
		$sql = "SELECT e.id, p.rate FROM employees e 
				JOIN position p ON e.position_id = p.id 
				WHERE e.employee_id = '$employee'";
		$query = $conn->query($sql);
		
		if($query->num_rows < 1){
			$_SESSION['error'] = 'Employee not found';
		}
		else{
			$row = $query->fetch_assoc();
			$employee_id = $row['id'];
			$rate = $row['rate'];

			// Calculate max advance for 2 weeks (8 hours/day * 10 days)
			$max_advance = $rate * 8 * 10;

			// Calculate total advances in last 2 weeks
			$sql_adv = "SELECT SUM(amount) AS total_advance FROM cashadvance 
						WHERE employee_id = '$employee_id' 
						AND date_advance >= DATE_SUB(NOW(), INTERVAL 14 DAY)";
			$result = $conn->query($sql_adv);
			$data = $result->fetch_assoc();
			$total_advance = $data['total_advance'] ?? 0;

			// Check if new advance + total advance exceeds max allowed
			if(($amount + $total_advance) > $max_advance){
				$_SESSION['error'] = "Amount exceeds maximum allowed advance for 2 weeks (₱" . number_format($max_advance, 2, '.', ',') . ")";
			}
			else{
				$sql_insert = "INSERT INTO cashadvance (employee_id, date_advance, amount) VALUES ('$employee_id', NOW(), '$amount')";
				if($conn->query($sql_insert)){
					$_SESSION['success'] = 'Cash Advance added successfully';
				}
				else{
					$_SESSION['error'] = $conn->error;
				}
			}
		}
	}	
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: cashadvance.php');
?>
