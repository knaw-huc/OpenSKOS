<?php
/**
 * OpenSKOS
 *
 * LICENSE
 *
 * This source file is subject to the GPLv3 license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenSKOS
 * @package    OpenSKOS
 * @copyright  Copyright (c) 2011 Pictura Database Publishing. (http://www.pictura-dp.nl)
 * @author     Mark Lindeman
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 */

class OpenSKOS_View_Helper_HumanFilesize {
	
	const PRECISION = 2;
	
	public function humanfilesize($bytes, $precision = null) {
		if (null===$precision) $precision = self::PRECISION;
	    $units = array('b', 'kb', 'Mb', 'Gb', 'Tb');
	  
	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	    $pow = min($pow, count($units) - 1);
	  
	    $bytes /= pow(1024, $pow);
	  
	    return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow]; 
	}
	
}
