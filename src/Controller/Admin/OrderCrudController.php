<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class OrderCrudController extends AbstractCrudController
{

    private $em;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $em,CrudUrlGenerator $crudUrlGenerator)
    {
        $this->em = $em;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation',' Prépatation en cours ','fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery',' Livraison en cours ','fas fa-truck')->linkToCrudAction('updateDelivery');
        return $actions
                ->add('detail' , $updatePreparation)
                ->add('detail' , $updateDelivery)
                ->add('index','detail');
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();

        if(!($order->getState() == 2))
        {
            $order->setState(2);
            $this->em->flush();
            $this->addFlash('notice',"<span style='color:green;'><strong>La commande ".$order->getReference()." est bien <u>en cours de préparation</u></strong></span>");
        }else{
            $this->addFlash('notice',"<span style='color:green;'><strong>La commande ".$order->getReference()." est  <u>déjà mis en  préparation</u></strong></span>");
        }
        //On pourra envoyer un mail à l'utilisateur pour l'informer que ça commande est préparation / livraison

        $url = $this->crudUrlGenerator->build()
                    ->setController(OrderCrudController::class)
                    ->setAction('index')
                    ->generateUrl();

        return $this->redirect($url);

    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();

        if(!($order->getState() == 3))
        {
            $order->setState(3);
            $this->em->flush();
            $this->addFlash('notice',"<span style='color:orange;'><strong>La commande ".$order->getReference()." est bien <u>en cours de livraison</u></strong></span>");
        }else{
            $this->addFlash('notice',"<span style='color:orange;'><strong>La commande ".$order->getReference()." est  <u>déjà mis en  livraison</u></strong></span>");
        }

        $url = $this->crudUrlGenerator->build()
                    ->setController(OrderCrudController::class)
                    ->setAction('index')
                    ->generateUrl();

        return $this->redirect($url);

    }

    public function configureCrud(Crud $crud):Crud
    {
        return $crud->setDefaultSort(['id'=>'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt')->setLabel('Paid '),
            TextField::new('user.getFirstName')->setLabel('User'),
            TextEditorField::new('delivery','delivery address')->onlyOnDetail(),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrierName')->setLabel('Carrier'),
            MoneyField::new('carrierPrice','Carrier price')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Product paid')->hideOnIndex()
        ];
    }
}
