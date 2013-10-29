<?php
/***************************************************************
* Copyright notice
*
* (c) 2007 Foundation For Evangelism (info@evangelize.org)
* (c) 2009 Georg Ringer <www.ringer.it>
* All rights reserved
*
* This file is part of the Web-Empowered Church (WEC)
* (http://webempoweredchurch.org) ministry of the Foundation for Evangelism
* (http://evangelize.org). The WEC is developing TYPO3-based
* (http://typo3.org) free software for churches around the world. Our desire
* is to use the Internet to help offer new life through Jesus Christ. Please
* see http://WebEmpoweredChurch.org/Jesus.
*
* You can redistribute this file and/or modify it under the terms of the
* GNU General Public License as published by the Free Software Foundation;
* either version 2 of the License, or (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This file is distributed in the hope that it will be useful for ministry,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the file!
***************************************************************/

/**
 * Demo class for userFuncs within the Typoscript Constant Editor.
 *
 * @author	Web-Empowered Church Team <developer@webempoweredchurch.org>
 * @author Georg Ringer <www.ringer.it>  
 */
class tx_constantsextended {
	
	/**
	 * Builds a record list of any table
	 * @param		array		$params:  Contains fieldName and fieldValue.
	 * @param		obj		$pObj:  Objet
	 * @return		string		HTML output
	 */
	function recordList($params, $pObj) {
		/* Pull the current fieldname and value from constants */
		$fieldName = $params['fieldName'];
		$fieldValue = $params['fieldValue'];

		// get the configuration
		$conf = $this->getConf($fieldName, $pObj);

		$table 		= $conf['table'];
		$where 		= ($conf['where']!='') ? $conf['where'] : '1=1';
		$orderBy 	= $conf['orderBy'];
		$limit 		= $conf['limit'];		
		

		if ($table == '') {
			return 'Table "'.$table.'" doesn\'t exit';
		}
		
		/* Construct the SQL query */
		$res = $GLOBALS['TYPO3_DB']->exec_selectQuery('*', $table, $where.t3lib_beFunc::deleteClause($table), '', $orderBy, $limit);
		
		/* Build the HTML select tag */
		$content = array();
		$content[] = '<select name="'. $fieldName .'">';
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$label = t3lib_beFunc::getRecordTitle($table, $row);
			
			/* If the current user matches the field value, mark it as default */
			if ($row['uid'] == $fieldValue) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			
			/* Build the option tag */
			$content[] = '<option value="'. $row['uid'] .'" '. $selected .'>'.$label.'</option>';
		}
		$content[] = '</select>';
		
		return implode(chr(10), $content);
	}
	
	
	/**
	 * Builds an input form that also includes the link popup wizard.
	 * @param		array		$params:  Contains fieldName and fieldValue.
	 * @param		obj		$pObj:  Objet
	 * @return		string		HTML output
	 */
	function page($params, $pObj) {
		/* Pull the current fieldname and value from constants */
		$fieldName = $params['fieldName'];
		$fieldValue = $params['fieldValue'];


		// get the configuration
		$conf = $this->getConf($fieldName, $pObj);
		$formName = ($conf['formName']!='') ? $conf['formName'] : 'editForm';
		
		$input = '<input name="'. $fieldName .'" value="'. $fieldValue .'" />';
		
		/* @todo 	Don't hardcode the inclusion of the wizard this way.  Use more backend APIs. */
		$wizard = '<a href="#" onclick="this.blur(); vHWin=window.open(\'../../../../typo3/browse_links.php?mode=wizard&amp;P[field]='. $fieldName .'&amp;P[formName]='.$formName.'&amp;P[itemName]='. $fieldName .'&amp;P[fieldChangeFunc][typo3form.fieldGet]=null&amp;P[fieldChangeFunc][TBE_EDITOR_fieldChanged]=null\',\'popUpID478be36b64\',\'height=300,width=500,status=0,menubar=0,scrollbars=1\'); vHWin.focus(); return false;">
								<img src="../../../../typo3/sysext/t3skin/icons/gfx/link_popup.gif" width="16" height="15" border="0" alt="Link" title="Link" />
							</a>';
		
		return $input.$wizard;
	}

	/**
	 * Show an image, mainly for helping people (manual, ...)
	 * @param		array		$params:  Contains fieldName and fieldValue.
	 * @param		obj		$pObj:  Objet
	 * @return		string		HTML output
	 */
	function image($params, $pObj) {
		/* Pull the current fieldname and value from constants */
		$fieldName = $params['fieldName'];
		$fieldValue = $params['fieldValue'];


		// get the configuration
		$conf = $this->getConf($fieldName, $pObj);
		$src = $conf['file'];
		
		$image = '<img src="../../../../'.$src.'" />';
	
		return $image;
	}
	
	
	/**
	 * Show an iframe
	 * @param		array		$params:  Contains fieldName and fieldValue.
	 * @param		obj		$pObj:  Objet
	 * @return		string		HTML output
	 */
	function iframe($params, $pObj) {
		/* Pull the current fieldname and value from constants */
		$fieldName = $params['fieldName'];
		$fieldValue = $params['fieldValue'];


		// get the configuration
		$conf = $this->getConf($fieldName, $pObj);
		$settings = '';
		
		$conf['src'] = ($conf['https']==1) ? 'https://'.$conf['src'] : 'http://'.$conf['src'];
		unset ($conf['https']);

		
		foreach ($conf as $key=>$value) {
			$settings.= $key.'="'.$value.'" ';
		}
				
		$iframe = '<iframe '.$settings.' ></iframe>';
	
		return $iframe;
	}	
	
	
	/**
	 * Show an image, mainly for helping people (manual, ...)
	 * @param		array		$params:  Contains fieldName and fieldValue.
	 * @param		obj		$pObj:  Objet
	 * @return		string		HTML output
	 */
	function html($params, $pObj) {
		/* Pull the current fieldname and value from constants */
		$fieldName = $params['fieldName'];
		$fieldValue = $params['fieldValue'];


		// get the configuration
		$conf = $this->getConf($fieldName, $pObj);
		
		$search = array('#58#', '#59#', '#44#');
		$replace = array(':', ';', ',');
		$code = str_replace($search, $replace, $conf['code']);
	
		return $code;
	}


	/**
	 * Builds an textarea
	 * @param		array		$params:  Contains fieldName and fieldValue.
	 * @param		obj		$pObj:  Objet
	 * @return		string		HTML output
	 */
	function textarea($params, $pObj) {
		/* Pull the current fieldname and value from constants */
		$fieldName = $params['fieldName'];
		$fieldValue = $params['fieldValue'];

		// get the configuration
		$conf = $this->getConf($fieldName, $pObj);
		$key = substr($fieldName, 5,-1);
		$formName = ($conf['formName']!='') ? $conf['formName'] : 'editForm';
		
		$css = '';
		unset($conf['formName']);
		foreach ($conf as $key=>$value) {
			$css .= $key.':'.$value.'; ';
		}
		
		if ($css!='') {
			$css = ' style="'.$css.'" ';
		}
		
		$fieldValue = str_replace('#####', "\n", $fieldValue);
		$field = '<textarea '.$css.' id="field'.$key.'" name="'. $fieldName .'">'. $fieldValue .'</textarea>';
		
		// add necessary js modification to change linefeeds

		$js = '
		<script type="text/javascript">

			window.onload=function(){
				var el = document.getElementsByName("'.$formName.'");
				Event.observe(el[0], "submit", changeContent, false);
			}
			
			function changeContent() {
				var val = $("field'.$key.'").value;
				str2 = val;  
				while(str2.indexOf("\n") != -1) { 
					str2 = str2.replace("\n", "#####"); 
				}  
	
				$("field'.$key.'").value = str2;
				
			}
		</script>
		
		';
		
		$field .= $js;

		return $field;
	}
	
	/**
	 * Get the possible configuration of a single field
	 * @param		string		$fieldName:  Name of the field
	 * @param		obj		$pObj:  Objet
	 * @return		array		configuration
	 */
	function getConf($fieldName, $pObj) {
		$key = substr($fieldName, 5,-1);
		$tempConf = '';
		$realConf = array();

		// get the complete line from constants and find the key 'settings'
		$conf = t3lib_div::trimExplode(';', $pObj->flatSetup[$key.'..']);
		foreach ($conf as $key=>$value) {
			if (strpos($value, 'settings') !== false) {
				$tempConf = $value;
			}
		}
		
		// if settings are found, split them accordingly
		if ($tempConf!='') {
			$tempConf = substr($tempConf, 9);
			
			$tempConf = t3lib_div::trimExplode(',', $tempConf);
			
			foreach ($tempConf as $key) {
				$split = t3lib_div::trimExplode(':', $key);
				$realConf[$split[0]] = $split[1];
			}
			
		}
		
		return $realConf;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/constantsextended/class.tx_constantsextended.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/constantsextended/class.tx_constantsextended.php']);
}
?>