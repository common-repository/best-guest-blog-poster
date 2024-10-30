jQuery(document).ready(function() {
	function isValidEmailAddress(emailAddress) {
		var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
		return pattern.test(emailAddress);
	};
	
	function resetForm($form) {
		$form.find('input:text, input:password, input:file, select, textarea').val('');
		$form.find('input:radio, input:checkbox')
			 .removeAttr('checked').removeAttr('selected');
	}
	


	var imageupload = jQuery('#gpImageOfArticle');
	jQuery(imageupload).css({visibility: 'hidden', position: 'absolute', left: '-9999px'});
	jQuery(imageupload).after('<span class="file-input-simulator" style="position: relative;"><input type="text" /><a href="#" class="fire-button">Upload Image</a></span>');
	
	jQuery('#signup-form').on('click', '.file-input-simulator .fire-button', function(e) {
		e.preventDefault();
		jQuery(imageupload).click();
	});

	jQuery(document).on('change','#gpImageOfArticle[type=file]', function (e) {
		var file = this.files[0];	
		var ext = jQuery(this).val().split('.').pop().toLowerCase();
		if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			jQuery.Zebra_Dialog('The file is not ant image, please try another one.');
			return false;
		} else {
			if (file.size > 2*1024*1024) {
				jQuery.Zebra_Dialog('Too large Image. Only image smaller than 2MB can be uploaded.');
				return false;
			} else {
				var val = jQuery(this).val();
				jQuery('.file-input-simulator input').val(val);
			}
		}
	});
	
    jQuery(document).on('submit', '#gp-send-form', function(e) {
   
		e.preventDefault();
		
		var maindiv = jQuery(this);
		var message = jQuery.trim( jQuery(this).find('textarea[name="gpContentOfArticle"]').val() );
		var title = jQuery.trim( jQuery(this).find('input[name="gpNameOfArticle"]').val() );
		var bio = jQuery.trim( jQuery(this).find('textarea[name="gpBioOfTheAuthor"]').val() );
		
		if( jQuery.isEmptyObject(title) ) {
			jQuery(this).find('input[name="gpNameOfArticle"]').addClass('error');
		} else {
			jQuery(this).find('input[name="gpNameOfArticle"]').removeClass('error');
		}
		if( jQuery.isEmptyObject(message) ){
			jQuery(this).find('textarea[name="gpContentOfArticle"]').addClass('error');
		} else {
			jQuery(this).find('textarea[name="gpContentOfArticle"]').removeClass('error');
		}
		if( jQuery.isEmptyObject(bio) ) {
			jQuery(this).find('textarea[name="gpBioOfTheAuthor"]').addClass('error');
		} else {
			jQuery(this).find('textarea[name="gpBioOfTheAuthor"]').removeClass('error');
		}

		if(jQuery.isEmptyObject(title) || jQuery.isEmptyObject(message) || jQuery.isEmptyObject(bio)) {
		return false;
		}
		else {
			jQuery(this).find('#submit-button').attr('disabled', '');
			var replyOptions = {
				beforeSubmit: function() {
					jQuery('#submit-button').after('<span id="gploader" class="loaderimg"></span>');
				},
				success:  function(result) {
					jQuery('#gploader').hide().remove();
					jQuery('#submit-button').removeAttr('disabled', '');
					jQuery('#signup-inner').slideUp(500);
					jQuery('#thank-you-message').prepend('<div class="gp-submit-result">'+result+'</div>').slideDown(500);
				}
			};
			jQuery(this).ajaxSubmit(replyOptions);
		}
	});
	
	jQuery(document).on('click', '#gp-submit-try-again', function(e) {
		jQuery('#gp-send-form')[0].reset();
		jQuery('#signup-inner').slideDown(500);
		jQuery('#thank-you-message').slideUp(500).find('.gp-submit-result').remove();
		return false;
	});

});

jQuery.fn.extend( {
	limiter: function(limit, elem) {
		jQuery(this).on("keyup focus", function() {
			setCount(this, elem);
		});
		function setCount(src, elem) {
			var chars = src.value.length;
			if (chars > limit) {
				src.value = src.value.substr(0, limit);
				chars = limit;
			}
			elem.html( limit - chars );
		}
		setCount(jQuery(this)[0], elem);
	}
});
