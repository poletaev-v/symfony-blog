<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /**
         * @var Post $entityInstance
         */
        $entityInstance->setAuthor($this->getUser());
        $entityInstance->setDescription(strip_tags($entityInstance->getDescription()));
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Post')
            ->setEntityLabelInPlural('Posts')
            ->setEntityPermission('ROLE_ADMIN')
            ->setDateFormat('medium');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            BooleanField::new('isActive'),
            IdField::new('id')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            DateTimeField::new('publishedAt')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            TextField::new('title'),
            SlugField::new('slug')
                ->setTargetFieldName('title'),
            AssociationField::new('category'),
            TextField::new('shortDescription'),
            TextEditorField::new('description'),
        ];
    }
}
