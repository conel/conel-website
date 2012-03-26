<?php
	## =======================================================================
	## extra_formAction_register												
	## =======================================================================
	## sample register action- takes just three fields 
	## =======================================================================
	function extra_formAction_error($id,$page_id,$params) {
		## all data is checked. so we only need to
		## check if the user already exist and if not register him
		## fetch the fields we need to enter into the db
		$structurefile = MATRIX_BASEDIR.'settings/datatypes/extra_form/'.$params['structure'].'.xml';
		if(!file_exists($structurefile)) {
			return array('status'=>false);
		}
		## okay open the form file and parse it
		$ctl_form = new formparser($structurefile);
		$ctl_form->parse();

		## fetch the data from the formfile
		$general_error = $ctl_form->getError();
		
		return array('status'=>false,'output'=>array($params['name']=>$general_error));
	}					
?>