<?php
/*
 * Portal Content Management System
 * Copyright (C) 2011  Hendrik Weiler
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Hendrik Weiler
 * @license    http://www.gnu.org/licenses/gpl.html
 * @copyright  2011 Hendrik Weiler
 */
class model_db_content extends Orm\Model
{

	public static $_table_name = 'en_content';

	public static $_properties = array('id', 'site_id', 'type','label', 'text','text2','text3', 'wmode', 'parameter', 'pictures', 'flash_file','form','refer_content_id','dimensions', 'sort','classname');

	public static function setLangPrefix($prefix)
	{
		self::$_table_name = $prefix . '_content';
	}

	public static function genStyleFromClassname($classname)
	{
		$html = 'style="float:left;width:';
		$type = 100;
		$value = 98;

		switch($classname)
		{
			case '':
			case 0:
			$value = 98;
			break;
			case 4:
			$value = 23;
			$type = 25;
			break;
			case 3:
			$value = 31;
			$type = 33;
			break;
			case 2:
			$value = 48;
			$type = 50;
			break;
			case 1:
			$value = 73;
			$type = 75;
			break;
		}

		$html .= $value . '%;"';

		$return = new \stdClass;
		$return->style = $html;
		$return->type = $type;
		$return->value = $value;

		return $return;
	}
}