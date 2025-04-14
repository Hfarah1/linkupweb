<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProduitRepository;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(ProduitRepository $productRepository, Request $request): Response
    {
        $products = $productRepository->findAll();
        $order = new Commande();
        $form = $this->createForm(CommandeType::class, $order);
        
        return $this->render('order/order.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/newOrder', name: 'newOrder')]
    #[Route('/newOrder', name: 'newOrder')]
public function newOrder(ManagerRegistry $mr, Request $req): Response {
    $em = $mr->getManager();
    
    // Create new order
    $commande = new Commande();
    $form = $this->createForm(CommandeType::class, $commande);
    $form->handleRequest($req);
    
    try {
        // Récupérer les données directement du formulaire
        $productId = $req->request->get('product_id');
        $quantity = intval($req->request->get('quantity', 1));
        
        // Récupérer la méthode de paiement soit du formulaire Symfony, soit d'un champ séparé
        if ($form->isSubmitted()) {
            $paymentMethod = $form->get('payment_method')->getData();
        } else {
            $paymentMethod = $req->request->get('selected_payment_method', 'cash_on_delivery');
        }
        
        if (!$productId) {
            throw new \Exception('Aucun produit sélectionné');
        }
        
        // Trouver le produit
        $produitRepository = $em->getRepository(Produit::class);
        $produit = $produitRepository->find($productId);
        
        if (!$produit) {
            throw new \Exception('Produit non trouvé: ' . $productId);
        }
        
        // Valider la quantité
        if ($quantity <= 0 || $quantity > $produit->getValue()) {
            throw new \Exception('Quantité invalide');
        }
        
        // Calculer le total (prix du produit * quantité + frais d'expédition)
        $subtotal = $produit->getPrice() * $quantity;
        $shipping = 3.00;
        $total = $subtotal + $shipping;
        
        // Définir les détails de la commande
        $commande->setUserId(2); // ID utilisateur par défaut - utiliser l'utilisateur actuel
        $commande->setOrderDate(new \DateTime());
        $commande->setStatus('pending');
        $commande->setTotal($total); 
        $commande->setProduit($produit);
        $commande->setPaymentMethod($paymentMethod);
        $commande->setCreatedAt(new \DateTime());
        $commande->setUpdatedAt(new \DateTime());
        
        $em->persist($commande);
        $em->flush();
        
        $this->addFlash('success', 'Commande créée avec succès');
        
        // Rediriger vers la liste des commandes
        return $this->redirectToRoute('order_list');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de la création de la commande: ' . $e->getMessage());
        return $this->redirectToRoute('order');
    }
}
    #[Route('/orders', name: 'order_list')]
    public function listOrders(CommandeRepository $commandeRepository): Response
    {
        // Get user's orders with product data
        $orders = $commandeRepository->findOrdersWithProducts(2);
        
        return $this->render('order/listorder.html.twig', [
            'orders' => $orders
        ]);
    }
    
    #[Route('/order/approve/{id}', name: 'order_approve', methods: ['GET', 'POST'])]
    public function approveOrder(int $id, EntityManagerInterface $em, CommandeRepository $commandeRepository): Response
    {
        $order = $commandeRepository->find($id);
        
        if (!$order) {
            $this->addFlash('error', 'Order not found');
            return $this->redirectToRoute('order_list');
        }
        
        // Update order status
        $order->setStatus('approved');
        $order->setUpdatedAt(new \DateTime());
        
        $em->flush();
        
        $this->addFlash('success', 'Order has been approved successfully');
        return $this->redirectToRoute('order_list');
    }
    
    #[Route('/order/{id}/update', name: 'order_update')]
    public function updateOrder(
        Request $request,
        CommandeRepository $commandeRepository,
        EntityManagerInterface $em,
        ProduitRepository $produitRepository,
        int $id
    ): Response {
        $conn = $em->getConnection();
$tables = $conn->executeQuery("SHOW TABLES")->fetchFirstColumn();
error_log('/////////////////////////////////////////////////////////////////////Tables disponibles: ' . implode(', ', $tables));

$columnExists = $conn->executeQuery(
    "SELECT COUNT(*) FROM information_schema.columns 
     WHERE table_schema = 'farah' AND table_name = 'commande' AND column_name = 'payment_method'"
)->fetchOne();
error_log('La colonne payment_method existe: ' . ($columnExists ? 'OUI' : 'NON'));


        // Find the order
        $commande = $commandeRepository->find($id);
        
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }
    
        // Optional: Validate CSRF token
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('update-order', $submittedToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }
    
        // Get form data
        $paymentMethod = $request->request->get('payment_method');
        $quantity = (int)$request->request->get('quantity', 0);
        
        // Log incoming data for debugging
        error_log('///////////////////////Update request data: ' . json_encode($request->request->all()));
        error_log('//////////////////////////////////////////Payment method received: ' . $paymentMethod);
    
        // Update the payment method if provided
        if ($paymentMethod) {
            // Set the payment method only if it's different or needs updating
            if ($commande->getPaymentMethod() !== $paymentMethod) {
                $commande->setPaymentMethod($paymentMethod);

                error_log('******************************************************Updated payment method: ' . $commande->getPaymentMethod());
            }
        }
    
        // Update the quantity and total if necessary
        if ($quantity > 0 && $commande->getProduit()) {
            $price = $commande->getProduit()->getPrice();
            $total = $price * $quantity;
            $commande->setTotal($total);
            $commande->setPaymentMethod($paymentMethod);

        }
    
        // Update the updated_at field
        $commande->setUpdatedAt(new \DateTime());
    

        $em->flush();
    
        // Log after flushing for debugging
        error_log('######################################################"After flush - Payment method: ' . $commande->getPaymentMethod());
        $conn = $em->getConnection();
        $sql = "SELECT payment_method FROM commande WHERE id = :id";
        $result = $conn->executeQuery($sql, ['id' => $id])->fetchOne();
        error_log('Valeur en base de données après flush: ' . ($result ?: 'NULL'));
        // Redirect back to the orders page with success flash message
        return $this->redirectToRoute('order_list');
    }
    
    #[Route('/order/delete/{id}', name: 'order_delete', methods: ['GET', 'POST'])]
    public function deleteOrder(int $id, EntityManagerInterface $em, CommandeRepository $commandeRepository): Response
    {
        $order = $commandeRepository->find($id);
        
        if (!$order) {
            $this->addFlash('error', 'Order not found');
            return $this->redirectToRoute('order_list');
        }
        
        $em->remove($order);
        $em->flush();
        
        return $this->redirectToRoute('order_list');
    }
    
    #[Route('/orders/mark-paid/{id}', name: 'order_mark_paid', methods: ['GET', 'POST'])]
    public function markOrderPaid(int $id, EntityManagerInterface $em, CommandeRepository $commandeRepository): Response
    {
        $order = $commandeRepository->find($id);
        
        if (!$order) {
            $this->addFlash('error', 'Order not found');
            return $this->redirectToRoute('order_list');
        }
        
        if ($order->getStatus() === 'approved') {
            $order->setStatus('paid');
            $order->setUpdatedAt(new \DateTime());
            
            $em->flush();
        } 
        return $this->redirectToRoute('order_list');
    }
    
    #[Route('/orders/save-changes', name: 'orders_save_changes', methods: ['POST'])]
    public function saveAllChanges(Request $request, EntityManagerInterface $em, CommandeRepository $commandeRepository): Response
    {
        // Get all order updates from the form submission
        $orderData = $request->request->all('orders') ?: [];

        
        foreach ($orderData as $orderId => $data) {
            $order = $commandeRepository->find($orderId);
            if (!$order) continue;
            
            $quantity = $data['quantity'] ?? null;
            $paymentMethod = $data['payment_method'] ?? null;
            
            if ($quantity !== null) {
                $produit = $order->getProduit();
                if ($produit && $quantity <= $produit->getValue()) {
                    // Calculate new total
                    $total = ($produit->getPrice() * intval($quantity));
                    $order->setTotal($total);
                }
            }
            
            if ($paymentMethod) {
                $order->setPaymentMethod($paymentMethod);
            }
            
            $order->setUpdatedAt(new \DateTime());
        }
        
        $em->flush();
        
        // Handle approved orders being marked as paid
        $approvedOrderIds = $request->request->all('approved_orders') ?: [];
                if (!empty($approvedOrderIds)) {
            foreach ($approvedOrderIds as $orderId) {
                $order = $commandeRepository->find($orderId);
                if ($order && $order->getStatus() === 'approved') {
                    $order->setStatus('paid');
                    $order->setUpdatedAt(new \DateTime());
                }
            }
            
            $em->flush();
        }
        
        return $this->redirectToRoute('order_list');
    }
}