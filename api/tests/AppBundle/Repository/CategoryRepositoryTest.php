<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class CategoryRepositoryTest extends TestCase
{
    public function testCategoryRepository()
    {
        $category = new Category();
        $category->setName('name');
        $category->setSlug('slug');
        $category->setIsVisible(false);

        $categoryRepository = $this->createMock(CategoryRepository::class);

        $categoryRepository->expects($this->any())
            ->method('findBySlug')
            ->willReturn($category);

        $objectManager = $this->createMock(ObjectManager::class);

        $objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($categoryRepository);

        $foundObj = $categoryRepository->findBySlug('slug');

        $this->assertEquals('name', $foundObj->getName());
    }

}