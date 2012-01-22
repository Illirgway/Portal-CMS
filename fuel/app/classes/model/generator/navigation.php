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
class model_generator_navigation extends model_db_navigation
{
	private static $_navigation = array();

	private static function _retrieveData($group_id)
	{
		$result = null;

		self::$_navigation = self::_collectData(0,$group_id);
	}

	private static function _collectData($parent,$group_id)
	{
		$result = array();

		$data = self::find('all',array(
			'where' => array('parent'=>$parent,'group_id'=>$group_id),
			'order_by' => array('sort'=>'ASC')
		));

		if(Uri::segment(2) == '' && Uri::segment(3) == '')
		{
			$site = model_generator_preparer::$currentSite;
			$startsite = model_generator_preparer::getParentsFromSite($site);
		}
		else
		{
			$startsite = array(
				'main' => new stdClass,
				'sub' => new stdClass
			);
			$startsite['main']->url_title = '';
			$startsite['sub']->url_title = '';
		}

		if(!is_object($startsite))
		{
			$startsite = array(
				'main' => new stdClass,
				'sub' => new stdClass
			);
			$startsite['main']->url_title = '';
			$startsite['sub']->url_title = '';
		}
		
		foreach($data as $nav)
		{
			$result[$nav->id] = array(
				'id' => $nav->id,
				'label' => $nav->label,
				'url_title' => $nav->url_title,
			);

			if(Uri::segment(2) == $nav->url_title || Uri::segment(3) == $nav->url_title
				|| isset($startsite['sub']->url_title) && $startsite['sub']->url_title == $nav->url_title || $startsite['main']->url_title == $nav->url_title)
				$result[$nav->id]['active'] = true;
			else
				$result[$nav->id]['active'] = false;

			$sub = self::find('all',array(
				'where' => array('parent'=>$nav->id,'group_id'=>$group_id),
				'order_by' => array('sort'=>'ASC')
			));

			if(!empty($sub))
			{
				$result[$nav->id]['sub'] = array_values(self::_collectData($nav->id));
			}
		}
		return array_values($result);
	}

	private static function _navToHTML()
	{
		$outer = View::factory('public/template/navigation_outer');

		$html = array();

		foreach(self::$_navigation as $key => $nav)
		{
			$search = model_db_site::find('first',array(
				'where' => array('navigation_id'=>$nav['id'])
			));
			
			if(empty($search) && !isset($nav['sub']))
				continue;
			
			$data = array();
			$data['active_class'] = '';
			$data['target'] = '_self';
			$data['label'] = $nav['label'];
			$data['link'] = Uri::create(model_generator_preparer::$lang . '/' . $nav['url_title']);
			if(isset($nav['sub']))
			{
				$data['link'] = Uri::create(model_generator_preparer::$lang . '/' . $nav['url_title'] . '/' . $nav['sub'][0]['url_title']);
			}
			
			if(!empty($search->redirect)) 
			{
				$data['target'] = '_blank';
				$data['link'] = $search->redirect;
			}
			
			if($nav['active'] == true)
				$data['active_class'] = 'active';
			
			$html[$key] = View::factory('public/template/navigation_inner',$data);

			if(isset($nav['sub']))
			{
				$outerSub = View::factory('public/template/navigation_outer');

				$innerHTML = array();

				foreach($nav['sub'] as $subKey => $sub)
				{
					$search = model_db_site::find('first',array(
						'where' => array('navigation_id'=>$sub['id'])
					));
					if(empty($search))
						continue;

					$subData = array();
					$subData['active_class'] = '';
					$subData['target'] = '_self';
					$subData['label'] = $sub['label'];
					$subData['link'] = Uri::create(model_generator_preparer::$lang . '/' . $nav['url_title'] . '/' . $sub['url_title']);
					$subData['target'] = '_self';
						
					if($sub['active'] == true)
						$subData['active_class'] = 'active';

					if(!empty($search->redirect))
					{
						$subData['target'] = '_blank';
						$subData['link'] = $search->redirect;
					}
						

					$innerHTML[$subKey] = View::factory('public/template/navigation_inner',$subData);
					$subData['active_class'] = '';
				}
				$innerHTML = str_replace('{{INNER}}','',implode('',$innerHTML));
				$outerSub = str_replace('{{INNER}}',$innerHTML,$outerSub);
				$html[$key] = str_replace('{{INNER}}',$outerSub,$html[$key]);
			}

			
			$html[$key] = str_replace('{{INNER}}','',$html[$key]);
			$data['active_class'] = '';
		}
		$inner = implode('',$html);
		return str_replace('{{INNER}}',$inner,$outer);
	}

	public static function getNaviAsArray($lang_id,$group_id)
	{
		self::$_navigation = array();
		model_db_navigation::setLangPrefix(model_db_language::idToPrefix($lang_id));
		self::_retrieveData($group_id);

		return self::$_navigation;
	}

	public static function render($group_id)
	{
		if(!model_generator_module::$navigation)
			return;
			
		if(!preg_match('#[0-9]#i',$group_id))
		{
			$search = model_db_navgroup::find('first',array(
				'where' => array('title'=>$group_id)
			));

			if(!empty($search))
				$group_id = $search->id;
		}

		self::_retrieveData($group_id);
		return self::_navToHTML();
	}
}