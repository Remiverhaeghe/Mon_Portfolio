<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator; 


class OrderCrudController extends AbstractCrudController
{
    private $entityManager; 
    private $adminUrlGenerator; 

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator )
    {
        $this->entityManager = $entityManager; 
        $this->adminUrlGenerator = $adminUrlGenerator; 
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation'); 
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery'); 
        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance(); 
        $order->setState(2); 
        $this->entityManager->flush(); 

        $this->addFlash('notice', "<span style='color:green;'><strong></strong>La commande".$order->getReference()." est en <u>cours de préparation.</u></span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl(); 
        
        return $this->redirect($url); 
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance(); 
        $order->setState(3); 
        $this->entityManager->flush(); 

        $this->addFlash('notice', "<span style='color:orange;'><strong></strong>La commande".$order->getReference()." est en <u>cours de livraison.</u></span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl(); 
        
        return $this->redirect($url); 
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'passé le'), 
            TextField::new('user.fullName', 'utilisateur'), 
            TextEditorField::new('delivery','Adresse de livraison')->formatValue(function ($value) { return $value; })->onlyOnDetail(),
            MoneyField::new('total','total produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice','frais de livraison')->setCurrency('EUR'),  
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0, 
                'Payée' => 1, 
                'Préparation en cours' => 2, 
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits Achetés')->hideOnIndex()
        ];
    }
    
}
