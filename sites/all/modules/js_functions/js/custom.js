 (function($) {
  $(document).ready(function() {
		    /*Menu for hospitals*/
	var checkArray = ["932", "940", "942", "1168", "1171"];
	var json_url = Drupal.settings.js_functions.json_url;
	$.getJSON(json_url, function(data) {
		//alert(JSON.stringify(data));
		var url = Drupal.settings.js_functions.url;
		var listItems = $("#nice-menu-2 > li");
		listItems.each(function (i) {
			var menu_classes = $(this).attr("class");
			split_classes = menu_classes.split(/ +/);
			//alert(split_classes[0]);
//if($(this).find("a").attr("href") == "galery);
			split_menu_id = split_classes[0].split("-");
			//alert(split_menu_id[1]);
			//alert($(this).html());
			var menu_child = 0;
			$(this).find('ul > li').each(function (i) {
				id = $(this).find("a").attr("href");
				//alert(id);
				split_str = id.split("/");
				match_id = split_str[split_str.length-1];
				success = 0;
				$.each(data, function(i, item) {
					if(item.tid == match_id) {
						success = 1;
					}
				});
				if(success == 1) {
					hs_url = url + '/'+ match_id + '/' +split_menu_id[1];
					$(this).find("a").attr("href", hs_url);
					menu_child = 1;
				} else {
					if($.inArray(split_menu_id[1],checkArray) != -1) {
						$(this).hide();
					}
				}
				$(this).find('ul > li').each(function (i) {
					id = $(this).find("a").attr("href");
					//alert(id);
					split_str = id.split("/");
					match_id = split_str[split_str.length-1];
					success = 0;
					$.each(data, function(i, item) {
						if(item.tid == match_id) {
							success = 1;
						}
					});
					if(success == 1) {
						hs_url = url + '/'+ match_id + '/' +split_menu_id[1];
						$(this).find("a").attr("href", hs_url);
					} else {
						if($.inArray(split_menu_id[1],checkArray) != -1) {
							$(this).hide();
						}
					}
				});				
			});
			if(menu_child == 0) {
				//alert($.inArray(split_menu_id[1],checkArray));
				if($.inArray(split_menu_id[1],checkArray) != -1) {
					$(this).hide();
				}
			} else {
				$(this).find("> a").attr("href", "#");
			}
		});
	
	if(Drupal.settings.js_functions.menu_id) {
		//alert(JSON.stringify(data));
		menuclass = 'menu-'+Drupal.settings.js_functions.menu_id;
		innertxt = $("."+menuclass).html();
		//alert(innertxt);
		$("#hs-sub-menu").html(innertxt);
		$("#hs-sub-menu > a").hide();
		$("#hs-sub-menu ul").css('display', 'block');
		$("#hs-sub-menu ul").css('visibility', 'visible');
	}
	
	});
	
	if($(".container .l-content h1").text() == "Create Rating") {
		var rating_text = Drupal.settings.js_functions.rating_text;
		$(".container .l-content h1").text(rating_text);
	}
	
	
	
});

})(jQuery);