<?php 


/**
* Render a twitter-boostrap styled button group.
* @label - Label of the button group
* @buttons - an array of array
*		in each array can be defined: label, url
*	@options - [optional] - array of options, actually only the property class is handled		
*/
class TwitterBootstrap_Helper_ButtonGroup
{
	public function buttonGroup($label = 'Label', $buttons = array(), $options = array()){
		$grp = "<div id=\"{$this->get('id',$options)}\" class=\"btn-group {$this->get('class',$options)}\" >";
		$grp .= "<a class=\"btn dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">";
	  $grp .= $label;
		$grp .= "<span class=\"caret\"></span>";
		$grp .= "</a>";
		$grp .= "<ul class=\"dropdown-menu\">";
		foreach ($buttons as $key => $button) {
			$grp.= "<li>";
			$grp.= $this->buildButton($button);
			$grp.= "</li>";
		}
		$grp .= "</ul>";
		$grp .= "</div>";
		return $grp;
	}	

	private function buildButton(array $button){
		$opts = $this->get('options', $button, array());

		$id = (array_key_exists('id',$opts))?"id=\"{$opts['id']}\"":'';
		$class = (array_key_exists('class',$opts))?"class=\"{$opts['class']}\"":'';
		$dataToggle = (array_key_exists('data-toggle',$opts))?"data-toggle=\"{$opts['data-toggle']}\"":'';

		return "<a {$id} {$class} ${dataToggle} href=\"{$this->get('url',$button,'#')}\">{$this->get('label',$button, 'Label')}</a>";
	}




	private function get($key,$array,$default = ''){
		return (array_key_exists($key, $array))?$array[$key]:$default;
	}

}

?>