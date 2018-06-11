<?php

namespace App\Controller;

use App\Entity\Burger;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Functions;
use Symfony\Component\HttpFoundation\Response;

class BurgerController extends Controller
{
    /**
     * @Route("/burger/get_all", name="burger")
     * @Method({"GET"})
     * @return Response
     */
    public function getAll()
    {
        $utils = new Functions();
        $repoBurger = $this->getDoctrine()->getManager()->getRepository(Burger::class);

        $burgers = $repoBurger->findAll();

        if(count($burgers) == 0){
            return $utils->createResponse(404, array(
                "error" => "There are no burgers",
            ));
        }
        else{

            $results = [];


            $rows = [];

            if(count($burgers) <=4){
                /** @var  $burger Burger*/
                foreach ($burgers as $burger){
                    $results [] = [
                        'burgerId' => $burger->getId(),
                        'name' => $burger->getName(),
                        'description' => $burger->getDescription(),
                        'price' => $burger->getPrice(),
                        'image' => $burger->getImage(),
                    ];
                }
                $rows [] = $results;
                return $utils->createResponse(200, $rows);
            }
            else {
                for ($i = 0; $i < count($burgers); $i++) {
                    if ($i % 4 == 0 && $i > 0) {
                        $rows [] = $results;
                        $results = [];

                    }
                    $results [] = [
                        'pizzaId' => $burgers[$i]->getId(),
                        'name' => $burgers[$i]->getName(),
                        'description' => $burgers[$i]->getDescription(),
                        'price' => $burgers[$i]->getPrice(),
                        'image' => $burgers[$i]->getImage(),
                    ];
                }
                $rows [] = $results;
                return $utils->createResponse(200, $rows);
            }
        }


    }
}
