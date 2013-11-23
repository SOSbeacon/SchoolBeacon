(function($){ $.fn.ActionMenu = function(text1, text2, options){

    var defaults = {
		delay : 0,       
        duration : 8000,  
		fadeDuration: 5000
	};
  
    var options = $.extend(defaults, options);
	
	return this.each(function(index) {	
		
		var $this = $(this);
		$ActionMenu = $('#ActionMenu');
		
		$ActionMenu.mouseover( function(e){
			clearTimeout($ActionMenu.data("hideTimeoutId"));
			$ActionMenu.data("showTimeoutId", setTimeout("$ActionMenu.show()",options.delay));
         })		
	
		$ActionMenu.mouseleave(function(e){
				clearTimeout($ActionMenu.data("showTimeoutId"));
				$ActionMenu.data("hideTimeoutId", setTimeout("$ActionMenu.hide()",options.duration));			
		});
			
		$this.mouseover( function(e){
			e = e ? e : window.event;
			
		    $ActionMenu.data("href",$this.attr("href"));
			$AMLinks = $ActionMenu.data("href").split('||');			
			
			if ($AMLinks[0] !='') {
                $('.Contact').attr('href',$AMLinks[0]);
			} else {
				$('.Contact').remove();
			}
			
			if ($AMLinks[1] !='') {
				$('.Services').attr('href',$AMLinks[1]);
			} else {
				$('.Services').remove();
			}	
   		    
   		    $linkText = $this.attr("name").split('||');
				
   		    if ($linkText[0] != '') {
   		    	$('.Contact').html($linkText[0]);
   		    }
   		    if ($linkText[1]  != '') {
		    	$('.Services').html($linkText[1]);
		    }
			
			//don't hide the menu if the mouse is over the element again
			clearTimeout($ActionMenu.data("hideTimeoutId"));
				
				 var top = $this.offset().top-25;
				 var left =  $this.offset().left + ($this.width() /2) - ($ActionMenu.width() / 2) +25;					 
				 $ActionMenu.css('left', left);
				 $ActionMenu.css('top', top);		
				 
			  $ActionMenu.data("showTimeoutId", setTimeout("$ActionMenu.show()",options.delay));
		});
		
		$this.mouseout(function(e){
			clearTimeout($ActionMenu.data("showTimeoutId"));
			$ActionMenu.data("hideTimeoutId", setTimeout("$ActionMenu.hide()",options.duration));
		 });
		
		$this.click(function(e){
		    e.preventDefault();
		});

	});

}})(jQuery);