/* WP post Notification admin js*/
jQuery(document).ready(function(){
	    jQuery(".wpn-tab").hide();
		jQuery("#div-wpn-general").show();
	    jQuery(".wpn-tab-links").click(function(){
		var divid=jQuery(this).attr("id");
		jQuery(".wpn-tab-links").removeClass("active");
		jQuery(".wpn-tab").hide();
		jQuery("#"+divid).addClass("active");
		jQuery("#div-"+divid).fadeIn();
		});
});
