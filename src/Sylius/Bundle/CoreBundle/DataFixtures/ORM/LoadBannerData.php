<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Model\VariantImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Finder\Finder;
use Tresepic\BoprBundle\Entity\Banner;

/**
 * Default product images.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadBannerData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $uploader = $this->get('sylius.image_uploader');
			
        $i = 1;
        $path = __DIR__.'/../../Resources/fixtures/banners';
        foreach ($finder->files()->in($path) as $img) {
            $banner = new Banner();
            $banner->setPriority($i);
            $banner->setFile(new UploadedFile($img->getRealPath(), $img->getFilename()));
            $uploader->upload($banner);

            $manager->persist($banner);

            $this->setReference('Sylius.Banner.'.$i, $banner);
            
            $i++;
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 11;
    }
}
