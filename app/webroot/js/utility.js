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
	
	// Makes tips balloons.
	// Called from layout because the root url is not sure here.
	$.initBalloons = function(urlToTipView){
		$('.tip').each(function(index, Element){
			var e = $(Element);
			var key = e.data('tipkey');
			e.balloon({
				url: urlToTipView + '/' + key,
				css: {
				  minWidth: "20px",
				  maxWidth: '150px',
				  padding: "5px",
				  borderRadius: "6px",
				  border: "solid 1px #777",
				  boxShadow: "4px 4px 4px #555",
				  color: "#666",
				  backgroundColor: "#efefef",
				  opacity: "0.85",
				  zIndex: "32767",
				  textAlign: "left",
				}
			});
		});
	};
	
})();
