"use strict"

// Send an ajax request to the specified URL with the
// default credentials and specified data;
// invoke closure_success upon success; closure_failed when failed.

async function SendRequest(url, data, closure_success = null, closure_failed = null)
{
	var success = undefined;
	
	var response = await $.ajax({
		type: "POST",
		url: url,
		data: data,
		headers: {
		  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
	})
	.done( function(response){success = true; return response;} )
	.fail( function(){success = false; return null;} );
	
	if( success === true && closure_success ) return closure_success(response);
	else if( success === false && closure_failed ) return closure_failed(response);
	else return response;
}