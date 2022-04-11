<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private const USER_ROLE_DEFAULT = 'ROLE_USER';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /**
         * @var User $entityInstance
         */
        $entityInstance->setPassword(
            $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword())
        );

        $roles = $entityInstance->getRoles();
        if (empty($roles)) {
            $roles[] = self::USER_ROLE_DEFAULT;
        }
        $entityInstance->setRoles($roles);
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            ->setEntityPermission('ROLE_ADMIN')
            ->setDateFormat('medium');
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        return [
            IdField::new('id')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            TextField::new('fullName'),
            TextField::new('username'),
            EmailField::new('email'),
            TextField::new('password')
                ->hideWhenUpdating(),
            ChoiceField::new('roles')
                ->setChoices(array_combine($roles, $roles))
                ->allowMultipleChoices(),
        ];
    }
}
