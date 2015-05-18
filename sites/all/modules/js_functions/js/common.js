(function($) {
	$(document).ready(function() {
		$("#edit-field-districts-tid").val('All');
		$(".home-video-download > a").attr("download", "1");
		//~ $("#edit-field-districts-tid").attr("name","edit-field-districts-tid");
		//~ $("#edit-field-hospital-type-tid").attr("name","edit-field-hospital-type-tid");
		//~ $("#edit-field-hospital-type-tid").attr("name","edit-field-hospital-type-tid");
		//~ 
		//~ $( '<div name="edit-field-districts-tid"></div>' ).insertAfter( ".content-top-first" );
		//~ $( '<div name="edit-field-hospital-type-tid"></div>' ).insertAfter( ".content-top-first" );
		//~ $( '<div name="edit-field-hospital-short-name-tid"></div>' ).insertAfter( ".content-top-first" );
		//~ 
		//~ $( "#edit-field-districts-tid" ).ufd({log:true});
		//~ 
		//~ $( "#edit-field-hospital-type-tid" ).ufd({log:true});
		//~ $( "#edit-field-hospital-short-name-tid" ).ufd({log:true});
	$("#edit-field-hospital-short-name-tid option[value=23]").hide();
		$("#edit-field-districts-tid").change(function () {
			var dist = $("#edit-field-districts-tid").val();
			var type = $( "#edit-field-hospital-type-tid").val();
			$.ajax({
				url: "hospitallist/json", //will return JsonResult
				type: "GET",
				data: { distid: dist, typeid : type },
				success: function (data) {
						//alert(data);
					  data = JSON.parse(data);
					  //alert(data.Value);
					  
					var ddlDivision = $('#edit-field-hospital-short-name-tid'); 
					ddlDivision.html(''); //clear previous contents.
					
					ddlDivision.append($('<option></option>')
						.val("All")
						.html("- Any -"));
					
					for (var key in data) {
						//~ alert(key);
						//~ alert(data[key]);
						ddlDivision.append($('<option></option>')
						.val(key)
						.html(data[key]));
					}
					
					$.each(data, function (index, item) {
					//alert(item.Value);
					 
					});
				 },
			})
		});
		
		$("#edit-field-hospital-type-tid").change(function () {
			var dist = $("#edit-field-districts-tid").val();
			var type = $( "#edit-field-hospital-type-tid").val();
			$.ajax({
				url: "hospitallist/json", //will return JsonResult
				type: "GET",
				data: { distid: dist, typeid : type },
				success: function (data) {
						//alert(data);
					  data = JSON.parse(data);
					  //alert(data.Value);
					  
					var ddlDivision = $('#edit-field-hospital-short-name-tid'); 
					ddlDivision.html(''); //clear previous contents.
					
					ddlDivision.append($('<option></option>')
						.val("All")
						.html("- Any -"));
					
					for (var key in data) {
						//~ alert(key);
						//~ alert(data[key]);
						ddlDivision.append($('<option></option>')
						.val(key)
						.html(data[key]));
					}
					
					$.each(data, function (index, item) {
					//alert(item.Value);
					 
					});
				 },
			})
		});
		$("#edit-field-hospital-short-name-tid").click(function () {
			var hospitaltid = $("#edit-field-hospital-short-name-tid").val();
			if(hospitaltid != 'All') {
				$('#views-exposed-form-browse-hospital-page .views-submit-button').html('<input type="button" id="edit-submit-browse-hospital" name="" value="Go" class="form-submit" onClick="redirecttohospitalhome('+hospitaltid+')">');
			} else {
				$('#views-exposed-form-browse-hospital-page .views-submit-button').html('<input type="submit" id="edit-submit-browse-hospital" name="" value="Go" class="form-submit">');
			}
		});
		$("#edit-submit-browse-hospital").click(function () {
			var hospitaltid = $("#edit-field-hospital-short-name-tid").val();
			
			if(hospitaltid != 'All') {
				alert(hospitaltid);
				var actURL = 'home/hospitals/'+hospitaltid+'/26/932';
				window.location.href = actURL;
				
			}
		});
		
    });                

})(jQuery);

function redirecttohospitalhome(hospitaltid) {
	var actURL = 'home/hospitals/'+hospitaltid+'/26/932';
				window.location.href = actURL;
}
