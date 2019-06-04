<?php


namespace App\DataFixtures;

use App\Entity\TodoList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ListFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $list = new TodoList();
        $list->setTitle('First lists');
        $manager->persist($list);

        $list = new TodoList();
        $list->setTitle('Second lists');
        $manager->persist($list);

        $list = new TodoList();
        $list->setTitle('Another lists');
        $manager->persist($list);

        $manager->flush();
    }
}