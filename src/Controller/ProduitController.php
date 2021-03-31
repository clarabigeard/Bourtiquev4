<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajoutPanier/{id}", name="produit_ajout_panier", methods={"POST"})
     */
    public function ajoutPanier(Request $request, SessionInterface $session, Produit $produit): Response{
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('ajout-panier', $request->request->get('token'))){
            //ajout dans la session du produit
            dd($session->get('id'));
            $lesProduits = $session->get('produits');
            if ($lesProduits == null){
                $lesProduits = [];
            }
            if (isset($lesProduits[$produit->getId()])){
                $leProduit=$lesProduits[$produit->getId()];
                $leProduit['qte']++;
            }else{
                $leProduit=array('qte'=>1,'produit'=>$produit);
            }
            $lesProduits[$produit->getId()]=$leProduit;
            $session->set("produits", $lesProduits);
        }
        return $this->redirectToRoute('produit_liste');
    }

    /**
     * @Route("/liste", name="produit_liste", methods={"GET"})
     */
    public function liste(ProduitRepository $produitRepository): Response{
        return $this->render('produit/liste.html.twig',['produits' => $produitRepository->findAll(),]);
    }

    /**
     * @Route("/viderPanier/", name="vider_panier")
     */
    public function viderPanier(SessionInterface $session): Response{
        $session->set("produits", []);

        return $this->redirectToRoute('produit_liste');
    }

    /**
     * @Route("/panier", name="voirPanier")
     */
    public function voirPanier(SessionInterface $session): Response{
        $lePanier = $session = $session->get('produits');
        return $this->render('panier/index.html.twig',['panier'=> $lePanier]);
    }

    /**
     * @Route("/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }
}
