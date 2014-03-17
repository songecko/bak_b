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
	}
}