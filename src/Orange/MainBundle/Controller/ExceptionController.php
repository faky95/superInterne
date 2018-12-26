<?php
namespace Orange\MainBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionController extends Controller {
	
	/**
	 * @var \Orange\MainBundle\Service\Mailer
	 */
	protected $mailer;
	
	/**
	 * @var \Swift_Transport
	 */
	protected $transport;
	
	public function __construct(\Twig_Environment $twig, $debug, $mailer, $transport)
	{
		$this->twig = $twig;
		$this->debug = $debug;
		$this->mailer = $mailer;
		$this->transport = $transport;
	}

	/**
	 * Converts an Exception to a Response.
	 * A "showException" request parameter can be used to force display of an error page (when set to false) or
	 * the exception page (when true). If it is not present, the "debug" value passed into the constructor will
	 * be used.
	 * @param Request              $request   The request
	 * @param FlattenException     $exception A FlattenException instance
	 * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
	 * @return Response
	 * @throws \InvalidArgumentException When the exception template does not exist
	 */
	public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
	{
		$currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
		$showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC
		$code = $exception->getStatusCode();
		if($showException==false && $code!=404) {
			//contact admin
 			$to = array("mamekhady.diouf@orange-sonatel.com", "madiagne.sylla@orange-sonatel.com");
 			$cc = array();
			$content = $this->twig->render('OrangeMainBundle:Exception:exception_full_bis.html.twig', array(
							'status_code' => $code, 'currentContent' => $currentContent, 'logger' => $logger, 'exception' => $exception,
							'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : ''
					));
			$dossier = __DIR__."/../../../../web/upload/bugs/".date("Y_m_d");
			if(!file_exists($dossier)) {
				mkdir($dossier, 0777, true);
			}
			$file = "bug-".Date("His").".html";
			$chemin = $dossier."/".$file;
			file_put_contents($chemin,$content);
			$sendMail = $this->mailer;
			$spool = $this->mailer->getMailer()->getTransport()->getSpool();
			$sendMail->sendBug($to, array(), "Erreur de traitement", $this->twig->render("OrangeMainBundle:Utilisateur:sendMailSupport.html.twig", array('file'=>$file, 'dossier'=>date("Y_m_d"), 'link'=>$request->getUri())),$dossier,$file);
			$spool->flushQueue($this->transport);
		}
		return new Response($this->twig->render($this->findTemplate($request, $request->getRequestFormat(), $code, $showException), array(
						'status_code' => $code, 'exception' => $exception, 'logger' => $logger, 'currentContent' => $currentContent,
						'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '')
				));
	
	}
	
	/**
	 * Converts an Exception to a Response.
	 * A "showApiException" request parameter can be used to force display of an error page (when set to false) or
	 * the exception page (when true). If it is not present, the "debug" value passed into the constructor will
	 * be used.
	 * @param Request              $request   The request
	 * @param FlattenException     $exception A FlattenException instance
	 * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
	 * @return Response
	 * @throws \InvalidArgumentException When the exception template does not exist
	 */
	public function showApiAction(Request $request, $exception, DebugLoggerInterface $logger = null)
	{
		$this->mailer->send(array('madiagne.sylla@orange-sonatel.com'), array(), "Erreur de traitement", $exception);
		return new JsonResponse(array('success' => false, 'exception' => true, 'message' => $exception->getMessage()));
	}
}
