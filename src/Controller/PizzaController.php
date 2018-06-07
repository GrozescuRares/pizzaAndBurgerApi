<?php

namespace App\Controller;

use App\Entity\Pizza;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Functions;
use Symfony\Component\HttpFoundation\Response;

class PizzaController extends Controller
{
    /**
     * @Route("/pizza/get_all", name="pizza")
     * @Method({"GET"})
     * @return Response
     */
    public function getAll()
    {
        $utils = new Functions();
        $repoPizza = $this->getDoctrine()->getManager()->getRepository(Pizza::class);

        $pizzas = $repoPizza->findAll();

        if(count($pizzas) == 0){
            return $utils->createResponse(404, array(
               "error" => "There are no pizzas",
            ));
        }
        else{

            $results = [];


            $rows = [];

            if(count($pizzas) <=4){
                /** @var  $pizza Pizza*/
                foreach ($pizzas as $pizza){
                    $results [] = [
                        'pizzaId' => $pizza->getId(),
                        'name' => $pizza->getName(),
                        'description' => $pizza->getDescription(),
                        'price' => $pizza->getPrice(),
                        'image' => $pizza->getImage(),
                    ];
                }
                $rows [] = $results;
                return $utils->createResponse(200, $rows);
            }
            else {
                for ($i = 0; $i < count($pizzas); $i++) {
                    if ($i % 4 == 0 && $i > 0) {
                        $rows [] = $results;
                        $results = [];

                    }
                    $results [] = [
                        'pizzaId' => $pizzas[$i]->getId(),
                        'name' => $pizzas[$i]->getName(),
                        'description' => $pizzas[$i]->getDescription(),
                        'price' => $pizzas[$i]->getPrice(),
                        'image' => $pizzas[$i]->getImage(),
                    ];
                }
                $rows [] = $results;
                return $utils->createResponse(200, $rows);
            }
        }

    }
}
