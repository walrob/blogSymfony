<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
         // create 20 post
         for ($i = 0; $i < 20; $i++) {
            $post = new Post();
            $post->setTitle('Post N° '.$i);
            $post->setContent('Texto para post número '.$i.PHP_EOL.
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque aliquam ligula id risus lacinia iaculis. Quisque mollis, leo et blandit volutpat, justo nisl venenatis justo, sed ultrices turpis arcu et massa. Ut sem nisi, sollicitudin ac vestibulum ac, imperdiet vel libero. Maecenas sed est mauris. Morbi nec augue a magna efficitur venenatis feugiat at lacus. Aenean volutpat blandit luctus. Duis pretium cursus erat ac convallis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Duis sem nisi, sodales sed blandit bibendum, dapibus nec odio. Nam non pharetra enim. Nulla facilisi. Donec est nisi, condimentum sed leo sed, lacinia imperdiet est. Etiam ornare turpis et nibh egestas, ac iaculis dolor facilisis. Nam id augue ut mi hendrerit laoreet. Quisque vitae blandit leo. Maecenas luctus orci ut erat suscipit, sit amet auctor odio porta.'.PHP_EOL.
            'Aliquam nec augue enim. Sed non ullamcorper tortor, eget dapibus neque. Integer in sodales ligula, vel iaculis nulla. Praesent imperdiet purus ac odio facilisis laoreet. Vivamus dictum mauris in ante semper, eget imperdiet risus consequat. Donec id massa dapibus, suscipit mauris quis, pharetra nunc. Duis porttitor aliquet arcu, sit amet sollicitudin arcu placerat non. Vestibulum vulputate turpis et nulla maximus, eu lacinia erat auctor.'.PHP_EOL.
            'Pellentesque tincidunt ipsum eu tellus vestibulum luctus. Phasellus id neque orci. Proin aliquet odio nunc, eu maximus tortor egestas eget. Nulla congue enim iaculis ligula sagittis, sit amet feugiat turpis consequat. Phasellus viverra porttitor porta. Sed pharetra dictum semper. Praesent faucibus eros vitae mauris porta, quis ullamcorper purus laoreet. In rutrum justo et dapibus rutrum. Integer faucibus diam non cursus aliquet. Phasellus ac mauris a orci gravida vestibulum quis eget sapien. Donec imperdiet arcu vitae neque tristique, nec vestibulum mauris vestibulum.');
            $post->setAuthor($manager->merge($this->getReference('user-'.rand(1,2))));
            $post->setCategory($manager->merge($this->getReference('category-'.rand(1,4))));
            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}