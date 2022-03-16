<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Classe\Cart;
use App\Classe\Mail; 

class OrderSuccessController extends AbstractController
{
    private $entityManager; 

    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager; 
    }

    /**
     * @Route("/commande/merci/{stripSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripSessionId($stripSessionId);

        if (!$order || $order->getUser() != $this->getUser() )
        {   
            return $this->redirectToRoute('home'); 
        }

        if ($order->getState() == 0)
        {   // vider la session cart
            $cart->remove(); 
            // Modifier le status State de la commande à 1 pour dire Payé
            $order->setState(1); 
            $this->entityManager->flush(); 

            // Envoyer un Mail à notre client pour confirmer sa commande
            $mail = new Mail(); 
            $content = "Bonjour". $order->getUser()->getFirstname()."<br/> Merci pour votre commande </br> Vous trouverez tous les produits qui vous font rêver"; 
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande sur ma_boutique est validée', $content);
        }
     
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
