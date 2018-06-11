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
