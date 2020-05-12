jQuery(document).ready(function($){

	$('#colorpicker').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
			$(el).css("backgroundColor", "#" + hex);
			$.ajax({
				 url : 'customcolor.php',
				 type : 'POST',
				 data : 'color=' + hex,
				 dataType : 'html',
				 success : function(code_html, statut){
							location.reload();
					}
			});
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
});