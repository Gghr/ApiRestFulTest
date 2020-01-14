<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;


class UsersController extends FOSRestController
{

    /**
     * @POST("/newuser")
     */
    public function newUserAction(Request $request)
    {
        $lastname = trim($request->get("lastname"));
        $firstname = trim($request->get("firstname"));

        if ($lastname == "" or $firstname == "") {
            $code = 400;
            $message = array("message" => "Le nom et le prénom de l'utilisateur sont manquant", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }
        if (is_numeric($lastname) or is_numeric($firstname)) {
            $code = 400;
            $message = array("message" => "Les paramètres doivent etre des chaines de caractère", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(array('lastname' => $lastname, 'firstname' => $firstname));
            if ($user == null) {
                $user = New User();
                $creationdate = new\ DateTime();
                $user->setFirstname(trim($firstname));
                $user->setLastname(trim($lastname));
                $user->setCreationdate($creationdate);
                $em->persist($user);
                $code = 200;
                $message = array("message" => "Utilisateur créé avec succes", "code" => $code);
                $view = $this->view($message)
                    ->setStatusCode($code)
                    ->setFormat("json");
                if ($code == 200)
                    $em->flush();
            } else {
                $code = 200;
                $message = array("message" => "Cet utilisateur existe déja", "code" => $code);
                $view = $this->view($message)
                    ->setStatusCode($code)
                    ->setFormat("json");
                $this->finalView = $view;
                return $this->handleView($this->finalView);
            }

        } Catch (\Exception $exception) {
            $code = 500;
            $message = array("message" => "erreur " . $exception->getMessage() . " Ligne " . $exception->getLine(), "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
        }

        $this->finalView = $view;
        return $this->handleView($this->finalView);
    }


    /**
     * @Rest\Get("/users")
     */
    public function getUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        if (count($users) == 0) {
            $code = 404;
            $message = array("message" => "Liste vide", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;

        }else{
            $code = 200;
            $view = $this->view($users)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
        }

        return $this->handleView($view);
    }


    /**
     * @POST("/user")
     */
    public function getUserAction(Request $request)
    {
        $lastname = trim($request->get("lastname"));
        $firstname = trim($request->get("firstname"));

        if ($lastname == "" and $firstname == "") {
            $code = 400;
            $message = array("message" => "Le nom ou le prénom de l'utilisateur est manquant", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }
        if (is_numeric($lastname) or is_numeric($firstname)) {
            $code = 400;
            $message = array("message" => "Le paramètre doit etre une chaine de caractère", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(array('lastname' => $lastname, 'firstname' => $firstname));

            if ($user == null) {
                $code = 404;
                $message = array("message" => "Aucun utilisateur retrouvé pour votre requete", "code" => $code);
                $view = $this->view($message)
                    ->setFormat("json");
            } else {
                $view = View::create($user);
                $view->setFormat('json');
                return $this->handleView($view);
            }

        } Catch (\Exception $exception) {
            $code = 500;
            $message = array("message" => "erreur " . $exception->getMessage() . " Ligne " . $exception->getLine(), "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
        }

        return $this->handleView($view);
    }


    /**
     *
     * @POST("/userupdate")
     */
    public function updateUserAction(Request $request)
    {
        $lastnameOld = trim($request->get("lastnameOld"));
        $firstnameOld = trim($request->get("firstnameOld"));
        $lastnameNew = trim($request->get("lastnameNew"));
        $firstnameNew = trim($request->get("firstnameNew"));

        if ($lastnameOld == "" or $firstnameOld == "" or $lastnameNew == "" or $firstnameNew == "") {
            $code = 400;
            $message = array("message" => "Des paramètres sont manquants", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }
        if (is_numeric($lastnameOld) or is_numeric($firstnameOld) or is_numeric($firstnameNew) or is_numeric($lastnameNew)) {
            $code = 400;
            $message = array("message" => "Les paramètre doivent etre des chaines de caractère", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(array('lastname' => $lastnameOld, 'firstname' => $firstnameOld));
            if ($user == null) {
                $code = 404;
                $message = array("message" => "Aucun utilisteur retrouvé pour votre requete", "code" => $code);
                $view = $this->view($message)
                    ->setFormat("json");
            } else {

                $user2 = $em->getRepository(User::class)->findOneBy(array('lastname' => $lastnameNew, 'firstname' => $firstnameNew));
                if ($user2 == null) {
                    $user->setFirstname($firstnameNew);
                    $user->setLastname($lastnameNew);
                    $user->setUpdatedate(new \DateTime());
                    $em->persist($user);
                    $code = 200;
                    $message = array("message" => "Utilisateur mis à jour", "code" => $code);
                    $view = $this->view($message)
                        ->setStatusCode($code)
                        ->setFormat("json");
                    if ($code == 200)
                        $em->flush();
                }else{
                    $code = 400;
                    $message = array("message" => "Cet utilisateur existe déjà", "code" => $code);
                    $view = $this->view($message)
                        ->setFormat("json");
                }


            }
        } Catch (\Exception $exception) {
            $code = 500;
            $message = array("message" => "erreur " . $exception->getMessage() . " Ligne " . $exception->getLine(), "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
        }

        return $this->handleView($view);
    }


    /**
     *
     * @POST("/deleteuser")
     */
    public function deleteUerAction(Request $request)
    {
        $lastname = trim($request->get("lastname"));
        $firstname = trim($request->get("firstname"));

        if ($lastname == "" and $firstname == "") {
            $code = 400;
            $message = array("message" => "Le nom ou le prénom de l'utilisateur est manquant", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }
        if (is_numeric($lastname) or is_numeric($firstname)) {
            $code = 400;
            $message = array("message" => "Le paramètre doit etre une chaine de caractère", "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
            $this->finalView = $view;
            return $this->handleView($this->finalView);
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(array('lastname' => $lastname, 'firstname' => $firstname));

            if($user == null){
                $code = 404;
                $message = array("message" => "L'utilisateur n'existe pas", "code" => $code);
                $view = $this->view($message)
                    ->setStatusCode($code)
                    ->setFormat("json");
                $this->finalView = $view;
                return $this->handleView($this->finalView);
            }
            $em->remove($user);
            $em->flush();

            $verif = $em->getRepository('AppBundle:User')->findOneBy(array('lastname' => $lastname, 'firstname' => $firstname));
            $code = 200;
            if ($verif == null) {
                $message = array("message" => "Suppression effectuee avec succes", "code" => $code);
                $view = $this->view($message)
                    ->setStatusCode($code)
                    ->setFormat("json");
            } else {
                $code = 500;
                $message = array("message" => "Erreur lors de la suppression de l'element", "code" => $code);
                $view = $this->view($message)
                    ->setStatusCode($code)
                    ->setFormat("json");
            }

        } Catch (\Exception $exception) {
            $code = 500;
            $message = array("message" => "erreur " . $exception->getMessage() . " Ligne " . $exception->getLine(), "code" => $code);
            $view = $this->view($message)
                ->setStatusCode($code)
                ->setFormat("json");
        }

        return $this->handleView($view);
    }
}