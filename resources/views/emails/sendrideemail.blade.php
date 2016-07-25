<html>
	<head>
	</head>
	<body>
			Dear <?php echo $name['username']?> ,<br>
			Your offer is been booked successfully  by <?php echo $name['bookedname']?>.below are the offer details.<br/>
			Source :<?php echo $name['source']?><br>
			Destination :<?php echo $name['destination']?><br>
			Passenger email : <?php echo $name['bookedemail']?><br>
			Seats book :<?php echo $name['seat']?><br>
			<?php
			if(isset($name['amount']))
			{
				?>
				Amount earn: <?php echo $name['amount']?> Rs. <br>
				<?php
			}
			?>
			Time :<?php echo $name['time']?><br>
			Date :<?php echo $name['date']?><br><br>

			Enjoy your ride<br><br>

			Thank you,<br>
			Members
	</body>
</html>