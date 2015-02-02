<?php

namespace Tresepic\BoprBundle\Menu;

use Sylius\Bundle\WebBundle\Menu\BackendMenuBuilder as Menu;
use Knp\Menu\ItemInterface;

class BackendMenuBuilder extends Menu
{
	protected function addAssortmentMenu(ItemInterface $menu, array $childOptions, $section)
	{
		parent::addAssortmentMenu($menu, $childOptions, $section);
		
		$menu->addChild('manufacturer', array(
				'route' => 'tresepic_bopr_backend_manufacturer_index',
				'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
		))->setLabel('Marcas');
		
		$menu->addChild('banner', array(
				'route' => 'tresepic_bopr_backend_banner_index',
				'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
		))->setLabel('Banners');
		
		$menu->addChild('testimonial', array(
				'route' => 'tresepic_bopr_backend_testimonial_index',
				'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
		))->setLabel('Testimonios');
	}
}