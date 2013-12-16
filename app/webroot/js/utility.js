(function(){
	// Fix console.info,warn,error,log of IE7,8
	if (typeof window.console === "undefined") {
		window.console = {};
	}
	var names = ['info', 'warn', 'error', 'log'];
	for(var n=0; n<names.length; n++){
		var name = names[n];
		if(typeof window.console[name] !== 'function')
			window.console[name] = function(){};
	}
	
	// $.ajax() wrapper. Post and get JSON.
	$.postJSON = function(params){
		if(typeof params === 'undefined'){
			console.error('Pass params for $.postJSON()');
			return;
		}
		
		var options = $.extend({
			cache: false,
			type: 'POST',
			dataType: 'JSON'
		}, params);
		
		return $.ajax(options);
	};
	
})();
