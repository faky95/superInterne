<?php
namespace Orange\MainBundle\Utils;

class Notification {
	
	public static function notification($helper, $subject, $membresEmail, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);		
		$helper->Notif($membresEmail,$subject, $body);
		
	}
	
	public static function notificationForActionEspace($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifActionEspace($membresEmail, $cc, $subject, $body);
	
	}
	public static function notificationAction($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifAction($membresEmail, $cc, $subject, $body);
	
	}
	public static function notificationActionSignalisation($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifActionSignalisation($membresEmail, $cc, $subject, $body);
	
	}
	public static function notificationWithCopy($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifWithCopy($membresEmail, $cc, $subject, $body, $entity->getCommentaire());
	
	}
	public static function notificationSignWithCopy($helper, $subject, $membresEmail, $cc, $commentaire, $entity) {
		$body = array ('commentaire' => $commentaire, 'entity' => $entity, 'titre'	=> $subject);
		$helper->NotifWithCopy($membresEmail, $cc, $subject, $body, $entity->getCommentaire());
	
	}
	
	public static function postUpdate($helper, $subject, $membresEmail, $commentaire, $changeset, $type) {
		$body = array ('commentaire' => $commentaire, 'changeSet' => $changeset, 'titre'	=> $subject, 'type' => $type);
		$helper->Notif($membresEmail, $subject, $body);
	}
}
