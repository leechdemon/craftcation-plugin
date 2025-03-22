<script>
	var workshopSelection = [];
	
	function cc_workshop_addOrder( order_req ) {
		/* Set Auth Variables */
		var login = 'ck_8cfcfdd35a286601de1552269f05115524e41549';
		var pass = 'cs_2fce71bfddebf49d8063c5085b96f5c97e89d153';
		const credentials = login+":"+pass;
		const encodedCredentials = btoa(credentials); // Use btoa() for Base64 encoding
		const authorizationHeader = "Basic " + encodedCredentials;	

		/* Assemble Order */
		const order = {
			payment_method: "bacs",
			payment_method_title: "Direct Bank Transfer",
			set_paid: true,
			status: 'processing',
			customer_id: '41005'
		};
		
		/* Assemble Line Items */
//		order_req = JSON.parse( '[ { "05": "60711" } ]' );
//		order_req = JSON.parse( order_req );
		var line_items = [];
//		for (const key in order_req) {
//			if (order_req.hasOwnProperty(key)) {
			var line_item = { 'product_id': 57486, 'quantity': 1 };
//			var line_item = { 'product_id': 60711, 'quantity': 1 };
//				var line_item = { 'product_id': order_req[key], 'quantity': 1 };
//				line_items.push( JSON.parse( JSON.stringify( line_item ) ) );
				line_items.push( JSON.parse( JSON.stringify( line_item ) ) );
//			}
//		}
		order.line_items = line_items;

		var url = 'https://www.craftcationconference.com/wp-json/wc/v3/orders';
		var payload = { method: 'POST', headers: { 'Authorization': authorizationHeader, 'Content-Type': 'application/json' } };
//			payload.body = JSON.stringify(orderData);
		payload.body = JSON.stringify(order);

		fetch(url, payload)
		.then(response => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			console.log( data )
		})

	}
	function cc_workshop_getWorkshopSelection(user_id) {
		/* Set Auth Variables */
		var login = 'ck_8cfcfdd35a286601de1552269f05115524e41549';
		var pass = 'cs_2fce71bfddebf49d8063c5085b96f5c97e89d153';
		const credentials = login+":"+pass;
		const encodedCredentials = btoa(credentials); // Use btoa() for Base64 encoding
		const authorizationHeader = "Basic " + encodedCredentials;	

		var url = 'https://www.craftcationconference.com/wp-json/wc/v3/orders?customer_id='+user_id;
//		var url = 'https://www.craftcationconference.com/wp-json/wc/v3/orders?customer_id='+user_id+'&status=processing&per_page=100';
		var payload = { method: 'GET', headers: { 'Authorization': authorizationHeader, 'Content-Type': 'application/json' } };
//			payload.body = JSON.stringify( {
//				status: "processing",
//			} );
//		payload.body = JSON.stringify(order);

		fetch(url, payload)
		.then(response => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			data.forEach(function( item ) {
				if( item.status == 'processing' ) {
					item.line_items.forEach(function( line_item ) {
						/* Don't forget to check for refunded.. */
						document.getElementById( line_item.product_id ).selected = true;
						var timeslot = document.getElementById( line_item.product_id ).parentElement.id;
						timeslot = timeslot.split("_");
						timeslot = timeslot[timeslot.length-1];
						workshopSelection[ timeslot ] = line_item.product_id;
						
//						document.getElementById( 'ws_workshop_timeslot_'+String(timeslot) ).innerHTML = line_item.name;
					})
					
					console.log( item );
				}
			} );
		})

//		console.log( workshopSelection );
	}
//	function cc_order_timeRemaining( duration ) {
//		let timeRemaining = duration;
//		let orderLimitRemaining = document.getElementById('orderLimitRemaining');
//		
//		// repeat with the interval of 2 seconds
//		let timerId = setInterval(() => function() {
//			timeRemaining--;
//			orderLimitRemaining.innerHTML = timeRemaining
//		}, 1000);
//
//		// after 5 seconds stop
////		setTimeout(() => { clearInterval(timerId); alert('stop'); }, duration );
//
//	}


	function cc_order_timeRemaining(duration) {
		let remainingTime = duration;
		let orderLimitRemaining = document.getElementById('orderLimitRemaining');

		const timer = setInterval(() => {
		if (remainingTime <= 0) {
			clearInterval(timer);
			document.getElementById('cc_orderLimit').innerHTML = '<a href="#" class="workshop_notes">Refresh Page</a>';
			return;
		}
			remainingTime--;
			orderLimitRemaining.innerHTML = remainingTime;
		}, 1000);
	}



</script>