<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use http\Env\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use Doctrine\DBAL\Exception as DBALExceptions;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use FOS\RestBundle\Request\ParamFetcher;

class CategoryApiController extends FOSRestController
{

    /**
     * fetches one category by id
     * @ApiDoc(
     *  resource=true,
     *  description="fetches one category by id",
     *  section="Categories"
     * )
     * @param $id integer
     * @Route("/api/categories/{id}", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCategory($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $view = $this->view($category);
        return $this->handleView($view);
    }

    /**
     * finds one category by slug
     * @ApiDoc(
     *  resource=true,
     *  description="finds one category by slug",
     *  section="Categories"
     * )
     * @param $slug string
     * @Route("/api/categories/find/{slug}", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCategory(string $slug)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findBySlug($slug);

        $view = $this->view($category);
        return $this->handleView($view);
    }

    /**
     * adds a category
     * @ApiDoc(
     *  resource=true,
     *  description="adds a category",
     *  section="Categories" 
     * )
     * @Post("/api/categories")
     * @ParamConverter("category", converter="fos_rest.request_body")
     * @param $category Category
     * @param $paramFetcher ParamFetcher
     * @RequestParam(name="name", requirements=".*", nullable=false, description="Category Name")
     * @RequestParam(name="slug", requirements=".*", nullable=false, description="Category Slug")
     * @RequestParam(name="parent_id", requirements="\d+", nullable=true, description="Parent ID ( optional )")
     * @RequestParam(name="is_visible", requirements="(0|1)", nullable=false, description="Is Visible")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCategory(Category $category, ParamFetcher $paramFetcher)
    {
        $parentCategoryId = $paramFetcher->get('parent_id');

        if ($parentCategoryId) {
            $parentCategory = $this->getDoctrine()->getRepository(Category::class)->find($parentCategoryId);
            if ($parentCategory instanceof Category) {
                $category->setParent($parentCategory);
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($category);

        try {
            $entityManager->flush();
            $view = $this->view($category);
        } catch (DBALExceptions\UniqueConstraintViolationException $exception){
            $view = $this->view(
                [
                    'code'=>HttpResponse::HTTP_CONFLICT,
                    'message'=>'a category with the provided slug already exists'
                ], HttpResponse::HTTP_CONFLICT
            );
        }

        return $this->handleView($view);
    }

    /**
     * edits visibility of a category
     * @ApiDoc(
     *  resource=true,
     *  description="edits visibility of a category",
     *  section="Categories"
     * )
     * @Patch("/api/categories/{id}/visibility/{isVisible}")
     * @param $id int
     * @param $isVisible bool ( 0 or 1 )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editCategory(int $id, bool $isVisible)
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $category = $repo->find($id);

        if ($category instanceof Category) {
            $entityManager = $this->getDoctrine()->getManager();
            $category->setIsVisible($isVisible);
            $entityManager->merge($category);
            $entityManager->flush();
        }

        $view = $this->view($category);
        return $this->handleView($view);
    }

}

