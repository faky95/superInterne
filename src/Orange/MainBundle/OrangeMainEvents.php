<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orange\MainBundle;

/**
 * Contains all events thrown in the Suivi entity
 */
final class OrangeMainEvents
{

    /**
     * The action events
     */
	const ACTION_ESPACE_CREATE_NOUVELLE				= 'orange_main.action.espace.creation';
    const ACTION_CREATE_NOUVELLE 					= 'orange_main.action.creation';
    const ACTION_VALIDATE							= 'orange_main.action.validate';
    const ACTION_FAITE			 					= 'orange_main.action.faite';
    const ACTION_CLOTURE							= 'orange_main.action.cloture';
    const ACTION_PAS_SOLDER							= 'orange_main.action.pas.solder';
    const ACTION_DEMANDE_ABANDON					= 'orange_main.action.demande.abandon';
    const ACTION_DEMANDE_ABANDON_ACCEPTEE			= 'orange_main.action.demande.abandon.acceptee';
    const ACTION_DEMANDE_ABANDON_REFUSEE			= 'orange_main.action.demande.abandon.refusee';
    const ACTION_DEMANDE_REPORT						= 'orange_main.action.demande.report';
    const ACTION_DEMANDE_REPORT_ACCEPTEE			= 'orange_main.action.demande.report.acceptee';
    const ACTION_DEMANDE_REPORT_REFUSEE				= 'orange_main.action.demande.report.refusee';
    const ACTION_PROPOSITION_PORTEUR				= 'orange_main.action.proposition.porteur';
    const ACTION_PROPOSITION_ANIMATEUR				= 'orange_main.action.proposition.animateur';
    const ACTION_VALIDATION_ANIMATEUR				= 'orange_main.action.validation.animateur';
    const ACTION_VALIDATION_MANAGER					= 'orange_main.action.validation.manager';
    const ACTION_REASSIGNATION  					= 'orange_main.action.reassignation';

    /**
     * The signalisation events
     */
    const SIGNALISATION_CREATE_NOUVELLE				= 'orange_main.signalisation.creation';
    const SIGNALISATION_PRISE_EN_CHARGE				= 'orange_main.signalisation.prise.en.charge';
    const SIGNALISATION_NON_PRISE_EN_CHARGE			= 'orange_main.signalisation.non.prise.en.charge';
    const SIGN_EN_REBOUCLAGE				        = 'orange_main.signalisation.en_rebouclage';
    const SIGNALISATION_EFFICACE				    = 'orange_main.signalisation.efficace';
    const SIGNALISATION_NON_EFFICACE				= 'orange_main.signalisation.non.efficace';
    const SIGNALISATION_REFORMULATION				= 'orange_main.signalisation.reformulation';
    
    /**
     * The action generique events
     * 
     */
    const ACTIONGENERIQUE_NOUVEAU 					 = 'orange_main.actiongenerique.creation';
    const ACTIONGENERIQUE_MODIFIE			         = 'orange_main.actiongenerique.modification';
    const ACTIONGENERIQUE_SOLDE 			         = 'orange_main.actiongenerique.solde';
    const ACTIONGENERIQUE_FAITE 					 = 'orange_main.actiongenerique.faite';
    const ACTIONGENERIQUE_ABANDON		             = 'orange_main.actiongenerique.abandon';
    const ACTIONGENERIQUE_ANNULE_DEMANDEABANDON	     = 'orange_main.actiongenerique.annule.demandeabandon';
    const ACTIONGENERIQUE_ANNULE_DEMANDEREPORT	     = 'orange_main.actiongenerique.annule.demandereport';
    
    
}
