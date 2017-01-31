<?php
namespace Orange\MainBundle\Utils;

use Orange\MainBundle\Service\Mailer;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Signalisation;
use Orange\MainBundle\Entity\Tache;

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
	 * @param Mailer $helper
	 * @param string $subject
	 * @param array $membresEmail
	 * @param array $cc
	 * @param string $commentaire
	 * @param Action|Signalisation|Tache $entity
	 */
	public static function notificationWithCopy($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifWithCopy($membresEmail, $cc, $subject, $body, $entity->getCommentaire());
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
}
