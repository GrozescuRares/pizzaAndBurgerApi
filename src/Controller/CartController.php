<?php

namespace App\Controller;

use App\Entity\Burger;
use App\Entity\Pizza;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Functions;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * @Route("/pizza/get_all_pizza")
     * @Method({"GET"})
     * @return Response
     */
    public function getAllPizza(){

        $utils = new Functions();

        $repoPizza = $this->getDoctrine()->getManager()->getRepository(Pizza::class);

        $pizzas = $repoPizza->findAll();
        $result = [];

        /* @var Pizza $pizza*/
        foreach ($pizzas as $pizza){
            $result [] = [
              'name' => $pizza->getName(),
            ];
        }

        if(count($result) > 0) {
            return $utils->createResponse(200, $result);
        }
        return $utils->createResponse(404, array(
           'errors' => 'There are no pizza'
        ));

    }

    /**
     * @Route("/burger/get_all_burgers")
     * @Method({"GET"})
     * @return Response
     */
    public function getAllBurgers(){

        $utils = new Functions();

        $repoBurger = $this->getDoctrine()->getManager()->getRepository(Burger::class);

        $burgers = $repoBurger->findAll();
        $result = [];

        /* @var Burger $burger*/
        foreach ($burgers as $burger){
            $result [] = [
                'name' => $burger->getName(),
            ];
        }

        if(count($result) > 0) {
            return $utils->createResponse(200, $result);
        }
        return $utils->createResponse(404, array(
            'errors' => 'There are no burgers'
        ));

    }



    /**
     * @Route("/cart/get_product", name="get_product")
     * @Method({"POST"})
     * @param $request
     * @return Response
     */
    public function getProduct(Request $request)
    {
        $name = $request->request->get('id');
        $utils = new Functions();

        if($name){

            $name = ucwords($name);
            $repoPizza = $this->getDoctrine()->getManager()->getRepository(Pizza::class);
            $pizza = $repoPizza->findOneBy(array(
                'name' => $name,
            ));

            if($pizza){
                return $utils->createResponse(200, array(
                    'price' => $pizza->getPrice(),
                    'name' => $pizza->getName(),
                    'description' => $pizza->getDescription(),
                ));
            }
            else{
                $repoBurger = $this->getDoctrine()->getManager()->getRepository(Burger::class);
                $burger = $repoBurger->findOneBy(array(
                   'name' => $name,
                ));

                if($burger){
                    return $utils->createResponse(200, array(
                        'price' => $burger->getPrice(),
                        'name' => $burger->getName(),
                        'description' => $burger->getDescription(),
                    ));
                }
                else {
                    return $utils->createResponse(404, array(
                        'errors' => 'There is no product with that id',
                    ));
                }
            }


        }
        else{
            return $utils->createResponse(403, array(
               'errors' => 'Product id missing.'
            ));
        }
    }
}
