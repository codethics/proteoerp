<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
    Copyright � 2007  Andres Hocevar

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA; or visit
    http://www.gnu.org/
*/

class Path {

	var $path = array();
	var $separator = "/";

	function setPath($path=NULL) {
		if(!empty($path))
			$this->append($path);
	}
	
	function append($path) {
		$parcial=explode($this->separator,$path);
		foreach($parcial AS $part){
			if(!empty($part))
				$this->path[]=$part;
		}
		//$this->path=array_filter($this->path, "empty");
	}

	function getPath() {
		$path=implode($this->separator,$this->path);
		return $this->separator.$path;
	}

}
?>