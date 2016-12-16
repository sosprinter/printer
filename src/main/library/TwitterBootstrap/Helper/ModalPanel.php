<?php 


/**
* Render a twitter-boostrap styled button group.
*/
class TwitterBootstrap_Helper_ModalPanel
{
	public function modalPanel($modalId,$anchorClass, $options = array()){

		$title = (array_key_exists('title', $options))?$options['title']:'Conferma';
		$message = (array_key_exists('message', $options))?$options['message']:'Confermi?';

		$modal = 
		"<div id=\"{$modalId}\" class=\"modal hide fade\">
		  <div class=\"modal-header\">
		    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
		    <h3>{$title}</h3>
		  </div>
		  <div class=\"modal-body\">
		    <p>{$message}</p>
		  </div>
		  <div class=\"modal-footer\">
		    <a href=\"#\" class=\"btn\" data-dismiss=\"modal\">Annulla</a>
		    <a href=\"#\" class=\"btn btn-primary\">Conferma</a>
		  </div>
		</div>";

		$script = "<script>
			\$(document).ready(function(){
				var \$modal = \$('#{$modalId}');
				$('.{$anchorClass}').each(function(i,anchor){
					var confirmHref = anchor.href;
					var \$anchor = \$(anchor);
					\$anchor.click(function(e){
						e.preventDefault();
						\$modal.find('.btn-primary').each(function(i,button){
							button.href = confirmHref;
						});
						\$modal.modal('show');
						return false;
					});
				});
			});
		</script>";

		return $modal.$script;
	}	

}

?>