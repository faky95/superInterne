<?php
namespace Orange\MainBundle\Utils;

use Orange\MainBundle\Service\Mailer;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Signalisation;
use Orange\MainBundle\Entity\Tache;
use Orange\MainBundle\Entity\ActionGenerique;
use Orange\MainBundle\Entity\ActionStatut;

class Notification {
	
	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param string $commentaire
	 * @param Action|Signalisation|Tache $entity
	 */
	public static function notification($helper, $subject, $membresEmail, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);		
		$helper->Notif($membresEmail,$subject, $body);
	}

	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param array $cc
	 * @param string $commentaire
	 * @param Action|Signalisation|Tache $entity
	 */
	public static function notificationForActionEspace($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifActionEspace($membresEmail, $cc, $subject, $body);
	}

	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param array $cc
	 * @param string $commentaire
	 * @param Action|Signalisation|Tache $entity
	 */
	public static function notificationAction($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifAction($membresEmail, $cc, $subject, $body);
	}

	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $target
	 * @param array $copy
	 * @param string $infos
	 * @param Tache $entity
	 */
	public static function notificationTache($helper, $subject, $target, $copy, $infos, $entity) {
		$body = array ('commentaire' => $infos, 'entity' => $entity, 'titre' => $subject);
		$helper->NotifTache($target, $copy, $subject, $body);
	}

	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param array $cc
	 * @param string $commentaire
	 * @param Action|Signalisation|Tache $entity
	 */
	public static function notificationActionSignalisation($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifActionSignalisation($membresEmail, $cc, $subject, $body);
	
	}

	/**
	 * @param Mailer $mailer
	 * @param string $subject
	 * @param array $membresEmail
	 * @param array $cc
	 * @param string $commentaire
	 * @param Action|ActionStatut|Signalisation|Tache $entity
	 */
	public static function notificationWithCopy($mailer, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$commentaire = ($entity instanceof ActionStatut) ? $entity->getCommentaire() : $commentaire;
		$mailer->NotifWithCopy($membresEmail, $cc, $subject, $body, $commentaire, $entity instanceof Tache);
	}

	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param array $cc
	 * @param string $commentaire
	 * @param Signalisation $entity
	 */
	public static function notificationSignWithCopy($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifWithCopy($membresEmail, $cc, $subject, $body, $entity->getCommentaire());
	}

	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param string $commentaire
	 * @param unknown $changeset
	 * @param string $type
	 */
	public static function postUpdate($helper, $subject, $membresEmail, $commentaire, $changeset, $type) {
		$body = array ('commentaire' => $commentaire, 'changeSet' => $changeset, 'titre'	=> $subject, 'type' => $type);
		$helper->Notif($membresEmail, $subject, $body);
	}
	
	/**
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param string $commentaire
	 * @param ActionGenerique $entity
	 */
	public static function notifActionGenerique($helper, $subject, $membresEmail, $cc=null,$commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->notifActionGenerique($membresEmail,$cc,$subject, $body);
	}
	
	public static function notificationWithCopyFromArray($datas,$helper) {
		foreach ($datas as $value){
		       $body = array ('commentaire' => $datas['content'], 'entity' => $datas['entity'], 'titre'	=> $datas['title']);
		       self::notificationWithCopy($helper, $datas['title'], $datas['to'], $datas['cc'], $datas['content'],  $datas['entity']);
		}
	}
	
}
