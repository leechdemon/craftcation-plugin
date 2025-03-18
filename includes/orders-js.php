<script>
	function api_test() {
		var login = 'ck_8cfcfdd35a286601de1552269f05115524e41549';
		var pass = 'cs_2fce71bfddebf49d8063c5085b96f5c97e89d153';
		const credentials = login+":"+pass;
        const encodedCredentials = btoa(credentials); // Use btoa() for Base64 encoding
        const authorizationHeader = "Basic " + encodedCredentials;

		var url = 'https://craftcationconference.com/wp-json/wc/v3/products/57491';

		fetch(url, {
          method: 'GET',
          headers: {
            'Authorization': authorizationHeader
          }
        })
        .then( (response) => response.json() )
        .then( (data) => {
			console.log(data);
		} )
        .catch(error => {
			console.log(error);
        });
	}	
</script>