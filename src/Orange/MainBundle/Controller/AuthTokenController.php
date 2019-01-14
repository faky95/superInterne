<?php
namespace Orange\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Orange\MainBundle\Form\CredentialsType;
use Orange\MainBundle\Entity\AuthToken;
use Orange\MainBundle\Entity\Credentials;

class AuthTokenController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("api/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('OrangeMainBundle:Utilisateur')
                ->findOneByEmail($credentials->getLogin());
        
        if (!$user) { 
            return $this->invalidCredentials();
        }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) { 
            return $this->invalidCredentials();
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return new JsonResponse($authToken);
    }

    /**
	 * @Rest\Get("api/list_action")
	 */
	public function listActionByApplicationAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$params = $request->query->all();
		$arrData = $em->getRepository('OrangeMainBundle:Action')->listByAction($params['application'],$params['date']);
		$output =  array(0 => array('id' => null, 'libelle' => 'Choisir une application ...'));
		foreach ($arrData as $data) {
			
			$output[] = array('id' => $data['id'], 'libelle' => $data['libelle'],
			'Reference'=>$data['reference'],
			'Type Action'=>$data['typeAction'],
		'date Debut'=>$data['dateDebut'],
		'date Initial'=>$data['dateInitial'],
		'date fin prevue'=>$data['dateFinPrevue'],
		'EtatCourant'=>$data['etatCourant'],
		'structure'=>$data['structure'],
		'porteur'=>$data['porteur'],
		
	);
	}
		return new JsonResponse($output);
	
		
	}

    private function invalidCredentials()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
    }
}
