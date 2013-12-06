(function(){
	
	// $.ajax() wrapper. Post and get JSON.
	$.postJSON = function(url, data){
		var options = {
			cache: false,
			type: 'POST',
			dataType: 'JSON'
		};
		if(typeof url != 'undefined') options.url = url;
		if(typeof data != 'undefined') options.data = data;
		
		var obj = $.ajax(options);
		
		return obj;
	};
	
})();
