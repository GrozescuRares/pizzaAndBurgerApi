<?php

namespace App\Controller;

use App\Entity\Burger;
use App\Entity\CustomerOrder;
use App\Entity\OrderedProduct;
use App\Entity\Pizza;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Functions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\DBAL\Driver\PDOException;

class OrderController extends Controller
{
    /**
     * @Route("/order/place_order", name="place_order")
     * @Method({"POST"})
     * @param $request
     * @return Response
     */
    public function place_order(Request $request)
    {
        $utils = new Functions();

        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);

            $customerName = $data['customerName'];
            $customerAddress = $data['customerAddress'];
            $customerEmail = $data['customerEmail'];
            $customerPhone = $data['customerPhone'];
            $total = $data['total'];
            $cart = $data['cart'];
            $emailMessage = "<html lang='en'><head></head><body><div style=\"position:fixed; margin : 0; padding : 0; top:0; left:0; background-image : url('http://localhost:82/Licenta/pizza&burger/img/burgerS1.jpg'); width : 100%; height:100%; background-attachment : fixed; background-position : center; background-size : cover; justify-content : center\">";

            if($customerName != "" and $customerAddress!="" and $customerEmail!= "" and $customerPhone != "" and $total > 0 and count($cart) > 0){

                $em = $this->getDoctrine()->getManager();

                //preparing customerOrder add in table
                $customerOrder = new CustomerOrder();
                $customerOrder->setCustomerName($customerName);
                $customerOrder->setCustomerAddress($customerAddress);
                $customerOrder->setCustomerEmail($customerEmail);
                $customerOrder->setCustomerPhone($customerPhone);
                $customerOrder->setTotal($total);

                $emailMessage.="<p style=\"padding-left:5%; padding-top:3%; color : #CEF0D4; font-family : 'Impact'; font-size:30px; font-weight : normal; line-height : 10px; margin : 0 0 15px; text-shadow : 1px 1px 2px #082b34\">
		Hello ".$customerName.",<br><br><br>
		Thank you for choosing us.<br><br><br><br>
		Here is your receipt:
		</p><br><br>
		<table style=\"margin-left:5%; border-radius:25px; border-collapse: collapse; width : 75%; height:30%; background-color : #A0522D\">
			<tr>
				<th style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>Name</th>
				<th style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>Unit Price</th>
				<th style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>Quantity</th>
				<th style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>Total Price</th>
			</tr>";

                $repoPizza = $this->getDoctrine()->getManager()->getRepository(Pizza::class);
                $repoBurger = $this->getDoctrine()->getManager()->getRepository(Burger::class);

                $em->persist($customerOrder);

                foreach ($cart as $product => $quantity){

                    $orderdProduct = new OrderedProduct();

                    $pizza = $repoPizza->findOneBy(array(
                        'name' => $product,
                    ));

                    if($pizza){
                        $orderdProduct->setPrice($pizza->getPrice());
                        $orderdProduct->setProductName($pizza->getName());
                        $orderdProduct->setQuantity($quantity);
                    }
                    else{

                        $burger = $repoBurger->findOneBy(array(
                            'name' => $product,
                        ));

                        if($burger){
                            $orderdProduct->setPrice($burger->getPrice());
                            $orderdProduct->setProductName($burger->getName());
                            $orderdProduct->setQuantity($quantity);
                        }
                    }
                    $orderdProduct->setCustomerOrderId($customerOrder);
                    $em->persist($orderdProduct);



                    try {
                        $em->flush();
                        $emailMessage.="<tr><td style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>".$orderdProduct->getProductName()."</td>
				<td style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>".$orderdProduct->getPrice()."$</td>
				<td style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>".$orderdProduct->getQuantity()."</td>
				<td style='color:white; font-size:20px; font-weight:normal; padding : 8px; border-bottom : 1px solid white; text-align:left'>".$orderdProduct->getPrice()*$quantity."$</td></tr>";
                    }
                    catch (Exception $ex){
                        return $utils->createResponse(409, array(
                            'errors' => 'Something went wrong'
                        ));
                    }
                    catch (PDOException $e){
                        return $utils->createResponse(409, array(
                            'errors' => 'Something went wrong'
                        ));
                    }

                }

                $emailMessage.="<tr>
				<td> </td>
				<td> </td>
				<td style=\"color:white; font-size:20px; font-weight:normal; padding : 8px; text-align : left\">Order Total:</td>
				<td style=\"color:white; font-size:20px; font-weight:normal; padding : 8px; text-align : left\">".$total."$</td>
			</tr></table>
		<p style=\"padding-left:5%; padding-top:5%; color : #CEF0D4; font-family : 'Impact'; font-size:30px; font-weight : normal; line-height : 10px; margin : 0 0 15px; text-shadow : 1px 1px 2px #082b34\">
			Have a nice day,<br><br><br>
			<a style=\"color : #CEF0D4; font-family : 'Impact'; font-size:30px; font-weight : normal; line-height : 10px; margin : 0 0 15px; text-shadow : 1px 1px 2px #082b34\" href=\"http://localhost:82/Licenta/pizza&burger/version3/index.php?action=home\" >Pizza & Burger </a>
		<br><br><br></p></div>
</body>
</html>";
                $header = "Content-Type: text/html; charset=ISO-8859-1\r\n";

                $post = [
                    "email" => $customerEmail,
                    "message" => $emailMessage,
                    "headers" => $header,
                ];

                if($utils->sendEmail($post)) {

                    return $utils->createResponse(200, array(
                        'success' => true
                    ));
                }
                else{
                    return $utils->createResponse(403, array(
                       'errors' => 'Confirmation email error'
                    ));
                }
            }
            else{
                return $utils->createResponse(403, array(
                   'errors' => 'Not all parameters were suplied'
                ));
            }

        }else{
            return $utils->createResponse(403, array(
               'errors' => "I didn't get a json"
            ));
        }
    }
}
