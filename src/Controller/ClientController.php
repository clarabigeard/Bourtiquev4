<?php

namespace App\Controller;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="client")
     */
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'lesClients' => $this->getDoctrine()->getRepository(Client::class)->findAll(),
        ]);
    }

    /**
     * @Route("/client/new", name="client_new")
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $leClient = new Client();
        $erreur = [];
        //est-ce que la page s'affiche suite ) une soumission de formulaire méthode post
        if ($request->isMethod('POST')){
            //les champs sont-ils valides ?
            $email = $request->request->get('email');
            if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                $leClient->setEmail($email);
            }else{
                $leClient->setEmail($email);
                $erreur['email'] = 'email non valide';
            }
            $nom = $request->request->get('nom');
            if (strlen($nom) > 0 && strlen($nom) <= 15 ){
                $leClient->setNom($nom);
            } else {
                $leClient->setNom($nom);
                $erreur['nom'] = 'nom trop long ou vide, ' . strlen($nom) . ' caractères sur 15';
            }
            $prenom = $request->request->get('prenom');
            $leClient->setPrenom($prenom);
            //le role est défini à Role_USER
            // A compléter avec d'autres rôles
            $leClient->setRoles(array("ROLE_USER"));
            $pass = $request->request->get('password');
            if (strlen($pass) >= 6){
                $passEncode = $userPasswordEncoder->encodePassword($leClient, $pass);
                $leClient->setPassword($passEncode);
            } else {
                $erreur['password'] = 'Mot de passe trop court 6 car min';
            }
            $telephone=$request->request->get('telephone');
            $leClient->setTelephone($telephone);
            $dateNaissance=$request->request->get('date_naissance');
            $leClient->setDateNaissance(new \DateTime($dateNaissance));

            //si aucune erreur détectée, on ajoute le clinet dans la table client
            if (count($erreur) == 0){
                $em = $this->getDoctrine()->getManager(); //entityManager
                $em->persist($leClient);
                $em->flush();
                return $this->redirectToRoute('client');
            }
        }
        return $this->render('client/new.html.twig',['leClient' => $leClient, 'erreur' => $erreur]);
    }

    /**
     * @Route("/client/edit/{id}", name="client_edit")
     */
    public function client_edit(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, Client $leClient): Response{

        $erreur = [];

        //est-ce que la page s'affiche suite une soumission de formulaire méthode post
        if ($request->isMethod('POST')){

            //les champs sont-ils valides ?
            $email = $request->request->get('email');
            if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                $leClient->setEmail($email);
            }else{
                $leClient->setEmail($email);
                $erreur['email'] = 'email non valide';
            }
            $nom = $request->request->get('nom');
            if (strlen($nom) > 0 && strlen($nom) <= 15 ){
                $leClient->setNom($nom);
            } else {
                $leClient->setNom($nom);
                $erreur['nom'] = 'nom trop long ou vide, ' . strlen($nom) . ' caractères sur 15';
            }
            //Todo : test sur prenom
            $prenom = $request->request->get('prenom');
            $leClient->setPrenom($prenom);

            //le role est défini à Role_USER
            // A compléter avec d'autres rôles
            $leClient->setRoles(array("ROLE_USER"));

            //Todo : test sur telephone
            $telephone=$request->request->get('telephone');
            $leClient->setTelephone($telephone);

            //Todo : test sur date naissance
            $dateNaissance=$request->request->get('date_naissance');
            $leClient->setDateNaissance(new \DateTime($dateNaissance));

            //si aucune erreur détectée, on ajoute le clinet dans la table client
            if (count($erreur) == 0){
                $em = $this->getDoctrine()->getManager(); //entityManager
                $em->persist($leClient);
                $em->flush();
                return $this->redirectToRoute('client');
            }
        }
        return $this->render('client/edit.html.twig',['leClient' => $leClient, 'erreur' => $erreur]);
    }

    /**
     * @Route("/client/delete/{id}", name="client_delete")
     */
    public function client_delete(Request $request, Client $leClient): Response{

        //le token est intégré comme champ hidden dans le formulaire, il est généré avec la clef 'delete-item'
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('delete-item', $request->request->get('token'))){
            //suppression validé par l'user
            $em = $this->getDoctrine()->getManager();
            $em->remove($leClient);
            $em->flush();
            return $this->redirectToRoute('client');
        }
        return $this->render('client/delete.html.twig',['leClient' => $leClient]);
    }
}
