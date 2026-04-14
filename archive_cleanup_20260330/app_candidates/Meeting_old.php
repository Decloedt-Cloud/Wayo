<?php

namespace App\Controllers;


class Meeting extends BaseController {
    public function __construct() {
        parent::__construct();
		// require_once APPPATH . '../vendor/autoload.php';
        // TODO: Replace with service: Config\Services::BigBlueButtonLibrary();
    }

    public function create() {
	
        $meetingID = 'demo123d';
        $meetingName = 'Réunion Démo';
        $moderatorPW = 'mod123';
        $attendeePW = 'att123';

        $response = $this->bigbluebuttonlibrary->createMeeting($meetingID, $meetingName, $moderatorPW, $attendeePW);
        // var_dump($response->getReturnCode())  ;die;
        if ($response->getReturnCode() === 'SUCCESS') {
            echo 'Meeting successfully created!';
        } else {
            echo 'Error: ' . $response->getMessage();
        }
    }

	public function join() {
		$meetingID = 'demo123'; // Assurez-vous que c'est une chaîne et non un tableau
		$fullName = 'Utilisateur Test';
		$password = 'att123';
		
		
		$joinURL = $this->bigbluebuttonlibrary->joinMeeting($meetingID, $fullName, $password);
		return redirect()->to($joinURL); // Redirige l'utilisateur vers l'URL de la réunion
	}

	public function isMeetingRunning($meetingID) {
		$response = $this->bigbluebuttonlibrary->isMeetingRunning($meetingID);
	
		if ($response->getReturnCode() === 'SUCCESS' && $response->isRunning()) {
			echo 'La réunion est en cours.';
			return true;
		} else {
			echo 'La réunion n’est pas encore démarrée.';
			return false;
		}
	}
	public function createAndJoinMeeting() {
        // Étape 1 : Créer une réunion
        $meetingID = 'demo1235ff';
        $meetingName = 'Réunion Démo';
        $moderatorPW = 'mod123';
        $attendeePW = 'att123';

        $response = $this->bigbluebuttonlibrary->createMeeting($meetingID, $meetingName, $moderatorPW, $attendeePW);
		
        if ($response->getReturnCode() === 'SUCCESS') {
            echo 'Réunion créée avec succès !';
            die;

            // Étape 2 : Obtenez le lien pour rejoindre en tant que modérateur
            $fullName = 'John Doe';
            $joinURL = $this->bigbluebuttonlibrary->joinMeeting($meetingID, $fullName, $moderatorPW);
			// $this->isMeetingRunning($meetingID);
            // Redirigez vers la réunion
            return redirect()->to($joinURL);
        } else {
            echo 'Erreur lors de la création de la réunion : ' . $response->getMessage();
        }
    }


	
    public function start()
    { 
        
        $meetingID   = 'testMeeting_'.uniqid(); 
        $meetingName = 'Test Meeting';
        $moderatorPW = 'modPW';
        $attendeePW  = 'attPW';

        $response = $this->bigbluebuttonlibrary->createMeeting($meetingID, $meetingName, $moderatorPW, $attendeePW);

        // die('rrrrrrr  : '.$response);
        if ($response->getReturnCode() === 'SUCCESS') {
            echo "Meeting successfully created!<br>";
            echo "Meeting ID: $meetingID<br>";
            
            // Vérifiez si la réunion est active
            $isRunning = $this->bigbluebuttonlibrary->isMeetingRunning($meetingID);
            // print_r($response);
            print_r($isRunning->isRunning());


            if (!$isRunning->isRunning()) {
                echo "The meeting is not running. Attempting to join as Moderator...<br>";

                // Joindre en tant que modérateur pour démarrer la réunion
                $moderatorJoinURL = $this->bigbluebuttonlibrary->joinMeeting(
                    $meetingID,
                    'Moderator',
                    $moderatorPW
                );
                // header("Location: $moderatorJoinURL");
                // exit;

                echo "<a href='$moderatorJoinURL' target='_blank'>Join as Moderator</a><br>";
            }

            // Générer l'URL pour un participant
            $attendeeJoinURL = $this->bigbluebuttonlibrary->joinMeeting(
                $meetingID,
                'Attendee',
                $attendeePW
            );

            echo "<a href='$attendeeJoinURL' target='_blank'>Join as Attendee</a><br>";
        } else {
    
            echo "Error: " . $response->getMessage();
        }
    }


    public function joinAsAttendee() {
        $meetingID = 'test123';
        $attendeePW = 'att123';

        // Générer l'URL pour rejoindre en tant que participant
        $attendeeJoinURL = $this->bigbluebuttonlibrary->joinMeeting(
            $meetingID,
            'Attendee',
            $attendeePW
        );

        return redirect()->to($attendeeJoinURL);
    }
	
	
}
